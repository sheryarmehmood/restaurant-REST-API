<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use Validator;

class DishController extends Controller
{

    //to fetch all dishes
    public function index()
    {
        // indexing paginated feedback 
        $dishes = Dish::paginate(1); // You can adjust the number of items per page as needed

        return response()->json(['data' => $dishes], 200);
    }


    //to save new dish
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), 
        [ 
            'name' => 'required|max:255|string|unique:dishes',
            'description' => 'required||max:255|string',
            'image_url' => 'required|url|max:255',
            'price' => 'required|numeric',
       ]);  

        if ($validator->fails()) {  

        return response()->json(['error'=>$validator->errors()], 401); 

        }  

        // Create a new dish
        $dish = Dish::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image_url' => $request->input('image_url'),
            'price' => $request->input('price'),
        ]);

        // Optionally, you can return the created dish as a response
        return response()->json(['message' => 'Dish created successfully', 'dish' => $dish], 201);
    }



    // Get details of a specific dish
    public function show($dishId)
    {
        // Find the dish by ID
        $dish = Dish::find($dishId);

        // Check if the dish is found
        if (!$dish) {
            return response()->json(['message' => 'No dish found'], 404);
        }

        // Return the details of the dish
        return response()->json(['data' => $dish], 200);
    }




    // Update an existing dish
    public function update(Request $request, $dishId)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => "required|max:255|string|unique:dishes,name,$dishId",
            'description' => 'required|max:255|string',
            'image_url' => 'required|url|max:255',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // Find the dish by ID
        $dish = Dish::find($dishId);

        // Check if the dish is found
        if (!$dish) {
            return response()->json(['message' => 'No dish found'], 404);
        }

        // Update the dish with the new data
        $dish->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image_url' => $request->input('image_url'),
            'price' => $request->input('price'),
        ]);

        // Optionally, you can return the updated dish as a response
        return response()->json(['message' => 'Dish updated successfully', 'dish' => $dish], 200);
    }



    //delete a dish
    public function destroy($dishId)
    {
        try {
            // Find the dish by ID
            $dish = Dish::findOrFail($dishId);

            // Delete the dish
            $dish->delete();

            // Return success response
            return response()->json(['message' => 'Dish deleted successfully'], 200);
        } catch (\Exception $exception) {
            // Dish not found
            return response()->json(['error' => 'Dish not found'], 404);
        }
    }



    //to rate the dish
    public function rateDish(Request $request, $dishId)
    {
        // Validate the request data
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        // Find the dish by ID
        $dish = Dish::findOrFail($dishId);

        // Update the dish rating
        $dish->update(['rating' => $request->input('rating')]);

        return response()->json(['message' => 'Dish rated successfully']);
    }
}