<?php

namespace App\Action\Migration;

use App\common\Action\BaseAction;
use App\common\Database\MigrationManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MigrationAction extends BaseAction
{
    private MigrationManager $migrationManager;

    public function __construct(MigrationManager $migrationManager)
    {
        $this->migrationManager = $migrationManager;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $action = $args['action'] ?? 'migrate';
            
            switch ($action) {
                case 'migrate':
                    $result = $this->migrationManager->migrate(true); // Suppress output for API
                    $data = [
                        'success' => $result['success'],
                        'message' => "Executed {$result['executed']} migrations",
                        'details' => $result
                    ];
                    $response->getBody()->write(json_encode($data));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

                case 'status':
                    ob_start();
                    $this->migrationManager->status();
                    $status = ob_get_clean();
                    
                    $data = [
                        'success' => true,
                        'status' => $status
                    ];
                    $response->getBody()->write(json_encode($data));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

                case 'rollback':
                    $steps = (int)($request->getQueryParams()['steps'] ?? 1);
                    $result = $this->migrationManager->rollback($steps);
                    $data = [
                        'success' => $result['success'],
                        'message' => "Rolled back {$result['rolled_back']} migrations",
                        'details' => $result
                    ];
                    $response->getBody()->write(json_encode($data));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

                case 'init':
                    $this->migrationManager->initialize();
                    $data = [
                        'success' => true,
                        'message' => 'Migrations table initialized'
                    ];
                    $response->getBody()->write(json_encode($data));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

                default:
                    $data = [
                        'success' => false,
                        'error' => 'Invalid action. Use "migrate", "status", "rollback", or "init"'
                    ];
                    $response->getBody()->write(json_encode($data));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

        } catch (\Exception $e) {
            $data = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
} 