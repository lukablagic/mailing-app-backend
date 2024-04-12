<?php

namespace Controller;

use Model\Folders;
use Utility\RequestHandler;

class FolderController
{
    private $folders;

    public function __construct($con)
    {
        $this->folders = new Folders($con);
    }

    public function getCollection($id, $action, $queryParams, $userData)
    {
        if ($id === 'all') {
            $folders = $this->folders->getAllFolders($userData['team_id']);
            RequestHandler::sendResponseArray(200, ['folders' => $folders, 'message' => 'Folders retrieved successfully!']);
        }
    }
    public function getResource($id, $action, $queryParams, $userData)
    {
    }
    public function postCollection($id, $action, $queryParams, $userData)
    {
    }
    public function putResource($id, $action, $queryParams, $userData)
    {
    }
    public function deleteResource($id, $action, $queryParams, $userData)
    {
    }
}
