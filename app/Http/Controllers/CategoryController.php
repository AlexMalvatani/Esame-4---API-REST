<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // Lista Categorie
    public function index()
    {
        $categories = Category::all();

        return response()->json(['categories' => $categories], 200);
    }

    // Visualizza Categoria Specifica
    public function showCategory($id)
    {
        $category = Category::find($id);

        if (!$category) {

            return response()->json(['error' => 'Category not found!'], 404);
        }

        return response()->json(['category' => $category], 200);
    }

    // Inserisci Nuova Categoria
    public function storeNewCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories',
                'description' => 'nullable|string',
            ], [
                'name.unique' => 'Category already exists!'
            ]);

            $category = Category::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
            ]);

            return response()->json(['message' => 'Category ' . $category->name . ' created succesfully'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Aggiorna Categoria
    public function modifyCategory(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found!'], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'Category already exists!'
        ]);

        $category->update($request->all());

        return response()->json(['message' => 'Category updated succesfully'], 200);
    }

    // Cancella Categoria
    public function deleteCategory($id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found!'], 404);
        }

        if ($category->trashed()) {

            return response()->json(['message' => 'Category ' . $category->name . ' is already deleted!']);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted succesfully'], 200);
    }

    // Ripristina Categoria Cancellata
    public function restoreCategory($id)
    {
        $category = Category::withTrashed()->where('id', $id)->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found!'], 404);
        }

        $category->restore();

        return response()->json(['message' => 'Category ' . $category->name . ' successfully restored!'], 200);
    }

    // Lista Categorie Cancellate
    public function listDeletedCategories()
    {
        $deletedCategories = Category::onlyTrashed()->get();

        if ($deletedCategories->isEmpty()) {
            return response()->json(['message' => 'No deleted categories found!']);
        } else {

            return response()->json(['deleted_categories' => $deletedCategories]);
        }
    }
}
