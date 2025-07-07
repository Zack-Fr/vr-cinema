<?php

class MovieService {

    public static function moviesToArray($cinema_db){
        $results = [];

        foreach($cinema_db as $c){
             $results[] = $c->toArray(); //hence, we decided to iterate again on the articles array and now to store the result of the toArray() which is an array. 
        } 

        return $results;
    }

}