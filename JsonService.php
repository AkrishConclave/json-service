<?php

class JsonService
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://jsonplaceholder.typicode.com';
    }

    public function getUsersWithPostsAndTodos()
    {
        $json = file_get_contents($this->apiUrl . '/users');
        $users = json_decode($json, true);

        $userData = [];

        foreach ($users as $user)
        {
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
        $json = file_get_contents($this->apiUrl . '/posts?userId=' . $userId);
        $posts = json_decode($json, true);

        return $posts;
    }

    public function getTodosByUserId($userId)
    {
        $json = file_get_contents($this->apiUrl . '/todos?userId=' . $userId);
        $todos = json_decode($json, true);

        return $todos;
    }

    public function addPost($userId, $title, $body)
    {
        $postData = [
            'userId' => $userId,
            'title' => $title,
            'body' => $body,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($postData),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($this->apiUrl . '/posts', false, $context);

        return json_decode($result, true);
    }

    public function editPost($postId, $title, $body)
    {
        $postData = [
            'title' => $title,
            'body' => $body,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'PUT',
                'content' => http_build_query($postData),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($this->apiUrl . '/posts/' . $postId, false, $context);

        return json_decode($result, true);
    }

    public function deletePost($postId)
    {
        $options = [
            'http' => [
                'method' => 'DELETE',
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($this->apiUrl . '/posts/' . $postId, false, $context);

        return $result !== false;
    }
}

// Использование класса
$jsonApi = new JsonService();

// Пример получения всех данных о пользователях, их постах и задачах
$allUserData = $jsonApi->getUsersWithPostsAndTodos();

// Пример добавления поста
$newPost = $jsonApi->addPost(1, 'New Title', 'New Body');

// Пример редактирования поста
$editedPost = $jsonApi->editPost(1, 'Updated Title', 'Updated Body');

// Пример удаления поста
$deleteResult = $jsonApi->deletePost(1);
