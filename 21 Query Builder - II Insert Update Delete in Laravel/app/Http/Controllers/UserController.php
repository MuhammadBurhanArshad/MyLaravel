<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUsers() {
        // Get all users
        $users = DB::table('users')->get();

        // Multiple where conditions (AND)
        $users = DB::table('users')->where('age', '>', 18)->where('city', 'karachi')->get(); // chained where methods
        $users = DB::table('users')->where([
            ['age', '>', 18],
            ['city', '=', 'karachi'],
        ])->get(); // array of conditions

        // OR condition
        $users = DB::table('users')->where('age', '>', 18)->orWhere('city', 'karachi')->get();

        // Range conditions
        $users = DB::table('users')->whereBetween('age', [18, 30])->get(); // age between 18 and 30
        $users = DB::table('users')->whereNotBetween('age', [18, 30])->get(); // age not between 18 and 30

        // Inclusion/exclusion conditions
        $users = DB::table('users')->whereIn('age', [18, 30])->get(); // age is either 18 or 30
        $users = DB::table('users')->whereNotIn('age', [18, 30])->get(); // age is neither 18 nor 30
        $users = DB::table('users')->orWhereNotIn('age', [18, 30])->get(); // OR condition with NOT IN

        // NULL conditions
        $users = DB::table('users')->whereNull('age')->get(); // where age is NULL

        // Date/time conditions
        $users = DB::table('users')->whereDate('created_at', '2023-01-01')->get(); // specific date
        $users = DB::table('users')->whereMonth('created_at', '01')->get(); // January
        $users = DB::table('users')->whereDay('created_at', '27')->get(); // Fixed note: this is whereDay, not whereDate
        $users = DB::table('users')->whereYear('created_at', '2023')->get(); // year 2023
        $users = DB::table('users')->whereTime('created_at', '08:01:34')->get(); // specific time

        // Selecting specific columns
        $users = DB::table('users')->select('name', 'email')->get(); // only name and email
        $users = DB::table('users')->select('name as full_name', 'email')->get(); // with aliases

        // Distinct values
        $users = DB::table('users')->select('city')->distinct()->get(); // unique cities (returns collection of objects)
        $cities = DB::table('users')->pluck('city'); // simple array of city values
        $cities = DB::table('users')->pluck('email', 'name'); // associative array (name => email)

        // Finding by primary key
        $user = DB::table('users')->find(1); // finds user with id = 1

        // ordering results
        $users = DB::table('users')->orderBy('name', 'asc')->get(); // ascending order by name
        $users = DB::table('users')->orderBy('created_at', 'desc')->get(); // descending order by created_at
        $users = DB::table('users')->latest()->get(); // getting result by created_at in descending order
        $users = DB::table('users')->oldest()->get(); // getting result by created_at in ascending order
        $users = DB::table('users')->inRandomOrder()->get(); // getting random order results

        // limit and offset
        $users = DB::table('users')->limit(10)->get(); // limit to 10 results
        $users = DB::table('users')->take(10)->get(); // limit to 10 results
        $users = DB::table('users')->skip(5)->take(10)->get(); // skip first 5 and limit to 10 results

        // counting results
        $count = DB::table('users')->count(); // total number of users
        $count = DB::table('users')->where('age', '>', 18)->count(); // count users older than 18
        $count = DB::table('users')->where('city', 'karachi')->count(); // count users in Karachi
        $count = DB::table('users')->whereBetween('age', [18, 30])->count(); // count users aged between 18 and 30
        $count = DB::table('users')->whereIn('age', [18, 30])->count(); // count users aged 18 or 30
        $count = DB::table('users')->whereNull('age')->count(); // count users with NULL age
        
        // aggregating results
        $maxAge = DB::table('users')->max('age'); // maximum age
        $minAge = DB::table('users')->min('age'); // minimum age
        $avgAge = DB::table('users')->avg('age'); // average age
        $sumAge = DB::table('users')->sum('age'); // sum of ages

        return view('users', ['data' => $users]);
    }

    public function getSingleUser($id) {
        $user = DB::table('users')->find($id);

        if (!$user) {
            return redirect()->route('usersList')->with('error', 'User not found');
        }

        return view('user', ['data' => $user]);
    }

    public function createUser(Request $request) {

        // insert add the record(s) in the database
        $user = DB::table('users')->insert([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'city' => $request->input('city'),
            'created_at' => now(),
            'updated_at' => now()
        ]); // we can multidimensional array to insert multiple users at once

        // insertOrIgnore will not insert if the record already exists
        $user = DB::table('users')->insertOrIgnore([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'city' => $request->input('city'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // upsert will insert if the record does not exist, or update it if it does
        $user = DB::table('users')->upsert([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'city' => $request->input('city'),
            'created_at' => now(),
            'updated_at' => now()
        ], ['email'], ['name', 'age', 'city', 'updated_at']);

        // insertGetId returns the ID of the inserted record
        $id = DB::table('users')->insertGetId([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'age' => $request->input('age'),
            'city' => $request->input('city'),
            'created_at' => now(),
            'updated_at' => now()
        ]); 

        if($user) {
            echo "<h1>Data Successfully inserted!</h1>";
        }

        return redirect()->route('usersView', ['id' => $id])->with('success', 'User created successfully');
    }

    public function updateUser(Request $request, $id) {
        // update the record in the database
        $user = DB::table('users')
            ->where('id', $id)
            ->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'age' => $request->input('age'),
                'city' => $request->input('city'),
                'updated_at' => now()
            ]);

        // updateOrInsert will update the record if it exists, or insert it if it does not
        $user = DB::table('users')->updateOrInsert(
            ['id' => $id], // conditions to find the record
            [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'age' => $request->input('age'),
                'city' => $request->input('city'),
                'updated_at' => now()
            ]
        );

        // increment and decrement methods
        $user = DB::table('users')->where('id', $id)->increment('age', 1); // increment age by 1
        $user = DB::table('users')->where('id', $id)->decrement('age', 1); // decrement age by 1
        $user = DB::table('users')->where('id', $id)->increment('age', 1, ['city' => $request->input('city')]); // update column with increment
        $user = DB::table('users')->where('id', $id)->incrementEach(['age' => 1, 'vote' => 2]); // update column with increment
        
        if($user) {
            echo "<h1>Data Successfully updated!</h1>";
        }

        return redirect()->route('usersView', ['id' => $id])->with('success', 'User updated successfully');
    }

    public function deleteUser($id) {
        // delete the record from the database
        $user = DB::table('users')->where('id', $id)->delete();

        // deleteOrFail will throw an exception if the record does not exist
        try {
            $user = DB::table('users')->where('id', $id)->deleteOrFail();
        } catch (\Exception $e) {
            return redirect()->route('usersList')->with('error', 'User not found');
        }

        if($user) {
            echo "<h1>Data Successfully deleted!</h1>";
        }

        return redirect()->route('usersList')->with('success', 'User deleted successfully');
    }
}
