<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;
use Framework\Authorization;

/** @package App\Controllers */
class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /** 
     * Show all listings
     * 
     * @return void
     */

    public function index()
    {

        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();
        loadView('listings/index', ['listings' => $listings]);
    }

    /**
     * Show create listing form
     * 
     * @return void
     */

    public function create()
    {
        loadView('listings/create');
    }

    /**
     * Show single listing
     * 
     * @param array $params
     * @return void
     */

    public function show($params)
    {
        $id = $params['id'] ?? '';
        $params = ['id' => $id];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        // Check if Listing already exists
        if (!$listing) {
            ErrorController::notFound('Listing not found!');
            return;
        }

        loadView('listings/show', ['listing' => $listing]);
    }
    /**
     * Store data in Database
     * 
     * @return void
     */
    public function store()
    {

        $allowedFields = [
            'title',
            'description',
            'salary',
            'tags',
            'company',
            'address',
            'city',
            'state',
            'phone',
            'email',
            'requirements',
            'benefits'
        ];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = Session::get('user')['id'];

        $newListingData = array_map('sanitize', $newListingData);



        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            // Reload view with errors
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            //sumit data

            $fields = [];
            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }
            $fields = implode(', ', $fields);

            $values = [];
            foreach ($newListingData as $field => $value) {
                //convert empmty strings to null
                if ($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }
            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);

            Session::setFlashMessage('success_message', 'Listing created successfully!');

            redirect('/listings');
        }
    }
    /** 
     * Delete the listing
     * @param mixed $name
     * @return void
     */
    public function destroy($params)
    {
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        // Check if listing exist
        if (!$listing) {
            ErrorController::notFound('Listing not found!');
            return;
        }

        // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorised to delete this listing!');
            redirect('/listings/' . $listing->id);
        }



        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        //set flash message
        Session::setFlashMessage('success_message', 'Listing deleted successfully!');
        redirect('/listings');
    } 

        /**
     * Show listing Edit form   
     * 
     * @param array $params
     * @return void
     */

     public function edit($params)
     {
                 $id = $params['id'] ?? '';
         $params = ['id' => $id];
 
         $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
 
         // Check if Listing already exists
         if (!$listing) {
             ErrorController::notFound('Listing not found!');
             return;
         }
 

         // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorised to edit this listing!');
            redirect('/listings/' . $listing->id);
        }
   

         loadView('listings/edit', ['listing' => $listing]);
     }

     /**
     * Update listing data in Database
     * 
     * @param array $$params
     * @return void
     */
    public function update($params) {

        $id = $params['id'] ?? '';
         $params = ['id' => $id];
 
         $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();
 
         // Check if Listing already exists
         if (!$listing) {
             ErrorController::notFound('Listing not found!');
             return;
         }

         // Authorization
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorised to update this listing!');
            redirect('/listings/' . $listing->id);
        }

         $allowedFields = [
            'title',
            'description',
            'salary',
            'tags',
            'company',
            'address',
            'city',
            'state',
            'phone',
            'email',
            'requirements',
            'benefits'
        ];

       $updateValues = [];

        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field).' is required!';

            }
        }
      
        if(!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit;
        } else {
            // submit to database
            $updateFields = [];

            foreach (array_keys($updateValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            }
           
            $updateFields = implode(', ', $updateFields);
            $updateQuery = "UPDATE listings SET {$updateFields} WHERE id = :id";

            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);

            Session::setFlashMessage('success_message', 'Listing updated successfully!');

            redirect('/listings/' . $id);

        
        }        
    }

    /**
     * Search for listings by Keywords/location
     * 
     * @return void
     */
    public function search() {
      
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        
        $query = "
        SELECT * FROM listings 
        WHERE 
            title LIKE :title 
            OR description LIKE :description 
            OR tags LIKE :tags 
            OR city LIKE :city 
            OR state LIKE :state
    ";
                        
    $params = [
        'title' => "%{$keywords}%",
        'description' => "%{$keywords}%",
        'tags' => "%{$keywords}%",
        'city' => "%{$keywords}%",
        'state' => "%{$keywords}%"
    ];

        $listings = $this->db->query($query, $params)->fetchAll();

        

        loadView('/listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }
}
