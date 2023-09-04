<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
 
use App\Models\Competitor;

class RegistrationController extends Controller
{
    /**
     * Show the form to create a new blog post.
     */
    public function create(Request $request): RedirectResponse
    {
      $validated = $request->validate([
        'first_name' => 'required',
      ]);

      $competitor = new Competitor();
      $competitor->given_name = $request['first_name'];
      $competitor->timestamps=false;
      $competitor->user_name = '300';
      $competitor->role_id = 4;

      $competitor->save();

        // Validate and store the blog post...
 
        //$post = /** ... */
 
      return to_route('checkout');
    }
}
