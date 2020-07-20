<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Notifications\YouWereInvited;
use Illuminate\Http\Request;

class PublishedQuestionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Question $question)
    {
        $this->authorize('update', $question);

        // Inspect the body of the reply for the username mentions
        preg_match_all('/@([^\s.]+)/',$question->content,$matches);

        $names = $matches[1];

        // And then notify user
        foreach ($names as $name){
            $user = User::whereName($name)->first();

            if($user){
                $user->notify(new YouWereInvited($question));
            }
        }


        $question->publish();
    }
}