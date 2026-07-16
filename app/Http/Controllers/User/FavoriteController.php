<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    /**
     * Display a listing of user favorites.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Favorite::where('user_id', auth()->id())
            ->with(['country.riskScore']);

        if ($search) {
            $query->whereHas('country', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso2', 'like', "%{$search}%")
                  ->orWhere('iso3', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
            });
        }

        $favorites = $query->paginate(10)->withQueryString();

        return view('user.favorites.index', compact('favorites', 'search'));
    }

    /**
     * Toggle country favorite status.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'notes' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $countryId = $request->input('country_id');
        $notes = $request->input('notes');

        $favorite = Favorite::where('user_id', $userId)
            ->where('country_id', $countryId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Country removed from favorites successfully.';
        } else {
            Favorite::create([
                'user_id' => $userId,
                'country_id' => $countryId,
                'notes' => $notes,
            ]);
            $message = 'Country added to favorites successfully.';
        }

        return redirect()->back()->with('success', $message);
    }
}
