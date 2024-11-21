<?php

namespace App\Models;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public static function searchHotels($params)
    {
        $client = ClientBuilder::create()->build();

        $query = [
            'index' => 'hotels',
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['name' => $params['name'] ?? '']],
                            ['match' => ['city' => $params['city'] ?? '']],
                            ['term' => ['star_rating' => $params['star_rating'] ?? '']],
                        ],
                    ],
                ],
            ],
        ];

        $results = $client->search($query);

        return $results['hits']['hits'];
    }
}

