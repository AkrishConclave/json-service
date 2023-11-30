<?php

/**
 * Класс JsonService предоставляет сервис для взаимодействия с внешним JSON API.
 *
 * Этот класс использует библиотеку Guzzle для выполнения HTTP-запросов
 *
 * @see https://jsonplaceholder.typicode.com/
 */

use GuzzleHttp\Client;

class JsonWithGuzzleService
{
    private $apiUrl;

    private $httpClient;

    public function __construct()
    {
        $this->apiUrl = 'https://jsonplaceholder.typicode.com';
        $this->httpClient = new Client();
    }

    public function getUsersWithPostsAndTodos()
    {
        $response = $this->httpClient->get($this->apiUrl . '/users');
        $users = json_decode($response->getBody(), true);

        $userData = [];

        foreach ($users as $user) {
            $userData[$user['id']]['user'] = $user;

            $posts = $this->getPostsByUserId($user['id']);
            $userData[$user['id']]['posts'] = $posts;

            $todos = $this->getTodosByUserId($user['id']);
            $userData[$user['id']]['todos'] = $todos;
        }

        return $userData;
    }

    public function getPostsByUserId($userId)
    {
        $response = $this->httpClient->get($this->apiUrl . '/posts?userId=' . $userId);
        $posts = json_decode($response->getBody(), true);

        return $posts;
    }

    public function getTodosByUserId($userId)
    {
        $response = $this->httpClient->get($this->apiUrl . '/todos?userId=' . $userId);
        $todos = json_decode($response->getBody(), true);

        return $todos;
    }

    public function addPost($userId, $title, $body)
    {
        $postData = [
            'userId' => $userId,
            'title' => $title,
            'body' => $body,
        ];

        $response = $this->httpClient->post($this->apiUrl . '/posts', [
            'form_params' => $postData,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function editPost($postId, $title, $body)
    {
        $postData = [
            'title' => $title,
            'body' => $body,
        ];

        $response = $this->httpClient->put($this->apiUrl . '/posts/' . $postId, [
            'form_params' => $postData,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function deletePost($postId)
    {
        $response = $this->httpClient->delete($this->apiUrl . '/posts/' . $postId);

        return $response->getStatusCode() === 200;
    }
}

// Использование класса
$jsonApi = new JsonWithGuzzleService();

// Пример получения всех данных о пользователях, их постах и задачах
$allUserData = $jsonApi->getUsersWithPostsAndTodos();

// Пример добавления поста
$newPost = $jsonApi->addPost(1, 'New Title', 'New Body');

// Пример редактирования поста
$editedPost = $jsonApi->editPost(1, 'Updated Title', 'Updated Body');

// Пример удаления поста
$deleteResult = $jsonApi->deletePost(1);
