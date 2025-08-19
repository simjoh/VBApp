<?php

namespace App\Action\Infra;

use App\common\Service\UnifiedSchedulerService;
use App\common\Service\CliLoggerService;
use App\common\Service\CliContainer;
use App\Commands\UnifiedCommandRegistry;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ScheduleAction
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $action = $args['action'] ?? 'list';
        
        switch ($action) {
            case 'list':
                return $this->listCommands($response);
            case 'run':
                return $this->runCommands($response);
            case 'execute':
                return $this->executeSpecificCommand($request, $response);
            case 'history':
                return $this->getHistory($response);
            default:
                return $this->errorResponse($response, 'Invalid action. Use: list, run, execute, or history', 400);
        }
    }

    private function listCommands(Response $response): Response
    {
        try {
            $pdo = $this->container->get(\PDO::class);
            $logger = new CliLoggerService();
            $cliContainer = new CliContainer($pdo);
            $scheduler = new UnifiedSchedulerService($pdo, $logger, $cliContainer);
            $commandRegistry = new UnifiedCommandRegistry($scheduler, $pdo, $logger, $cliContainer);
            
            $commandRegistry->registerAllCommands();
            $commands = $scheduler->getCommands();
            
            $commandList = [];
            foreach ($commands as $command) {
                $commandList[] = [
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'should_run' => $command->shouldRun(),
                    'status' => $command->shouldRun() ? 'READY' : 'WAITING'
                ];
            }

            $data = [
                'total_commands' => count($commandList),
                'commands' => $commandList
            ];

            $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error listing commands: ' . $e->getMessage() . ' - ' . $e->getTraceAsString(), 500);
        }
    }

    private function runCommands(Response $response): Response
    {
        try {
            $pdo = $this->container->get(\PDO::class);
            $logger = new CliLoggerService();
            $cliContainer = new CliContainer($pdo);
            $scheduler = new UnifiedSchedulerService($pdo, $logger, $cliContainer);
            $commandRegistry = new UnifiedCommandRegistry($scheduler, $pdo, $logger, $cliContainer);
            
            $commandRegistry->registerAllCommands();
            $results = $scheduler->runScheduledCommands();

            $data = [
                'execution_time' => date('Y-m-d H:i:s'),
                'total_commands_executed' => count($results),
                'results' => $results
            ];

            $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error running commands: ' . $e->getMessage(), 500);
        }
    }

    private function executeSpecificCommand(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $commandName = $queryParams['command'] ?? null;

        if (!$commandName) {
            return $this->errorResponse($response, 'Command parameter is required', 400);
        }

        try {
            $pdo = $this->container->get(\PDO::class);
            $logger = new CliLoggerService();
            $cliContainer = new CliContainer($pdo);
            $scheduler = new UnifiedSchedulerService($pdo, $logger, $cliContainer);
            $commandRegistry = new UnifiedCommandRegistry($scheduler, $pdo, $logger, $cliContainer);
            
            $commandRegistry->registerAllCommands();
            $result = $scheduler->executeCommandByName($commandName);

            $data = [
                'command' => $commandName,
                'execution_time' => date('Y-m-d H:i:s'),
                'result' => $result
            ];

            $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error executing command: ' . $e->getMessage(), 500);
        }
    }

    private function getHistory(Response $response): Response
    {
        try {
            $pdo = $this->container->get(\PDO::class);
            
            $sql = "SELECT * FROM scheduled_commands ORDER BY failed_at DESC LIMIT 50";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $history = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $data = [
                'total_entries' => count($history),
                'history' => $history
            ];

            $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return $this->errorResponse($response, 'Error getting history: ' . $e->getMessage(), 500);
        }
    }

    private function errorResponse(Response $response, string $message, int $statusCode): Response
    {
        $data = [
            'error' => $message,
            'status_code' => $statusCode
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
} 