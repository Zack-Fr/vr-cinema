<?php
require_once("Model.php");

class Movies extends Model
{

    private int $id;
    private string $title;
    private string $cast;
    private string $genre;
    private string $rating;
    private string $is_upcoming;
    private string $release_date;
    private string $description;

    protected static string $table = "movies";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->title = $data["title"];
        $this->cast = $data["cast"];
        $this->genre = $data["genre"];
        $this->rating = $data["rating"];
        $this->is_upcoming = $data["is_upcoming"];
        $this->release_date = $data["release_date"];
        $this->description = $data["description"];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCast(): string
    {
        return $this->cast;
    }
    public function getGenre(): string
    {
        return $this->genre;
    }

    public function getRating(): string
    {
        return $this->rating;
    }
    public function getIs_upcoming(): string
    {
        return $this->is_upcoming;
    }

    public function getRelease_date(): string
    {
        return $this->release_date;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    // ==================

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setCast(string $cast)
    {
        $this->cast = $cast;
    }

    public function setGenre(string $genre)
    {
        $this->genre = $genre;
    }

    public function setRating(string $rating)
    {
        $this->rating = $rating;
    }

    public function setIs_upcoming(int $is_upcoming)
    {
        $this->is_upcoming = $is_upcoming;
    }

    public function setRelease_date(string $release_date)
    {
        $this->release_date = $release_date;
    }
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
    public function toArray()
    {
        return [$this->id, $this->title, $this->cast, $this->genre, $this->rating, $this->is_upcoming, $this->release_date, $this->description];
    }

}

