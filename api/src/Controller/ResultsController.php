<?php

namespace App\Controller;

use App\common\Exceptions\BrevetException;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Service\EventService;
use App\Domain\Model\Result\Service\ResultService;
use App\Domain\Model\Track\Service\TrackService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;
use Slim\Views\Twig;

class ResultsController
{


    public function __construct(ContainerInterface   $c, ResultService $resultService, EventService $eventService, TrackService $trackService){
        $this->settings = $c->get('settings');
        $this->resultService = $resultService;
        $this->eventservice = $eventService;
        $this->trackService = $trackService;
    }




    //Resultat på text BRM2021
    public function getResultView(ServerRequestInterface $request, ResponseInterface $response, $args){
        $view = Twig::fromRequest($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $year = $route->getArgument('year');

         $event =  $this->eventservice->eventFor($eventUid, "");
        $result =  $this->resultService->resultsOnEvent($eventUid, $year);
        return $view->render($response, 'result.html', [
            'link' => $this->settings['path'] . "resultList/year/" . strval($args['year']) . "/event/" . $args['eventUid'], 'event' => json_encode($event)
        ]);
    }

    public function getTrackView(ServerRequestInterface $request, ResponseInterface $response, $args){
        $view = Twig::fromRequest($request);

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $event =  $this->eventservice->eventFor($eventUid, "");
        $tracks =  $this->trackService->tracksForEvent( "",$eventUid);

//        $result =  $this->resultService->trackContestants($eventUid, array());
        return $view->render($response, 'track.html', [
            'link' => $this->settings['path'] . "tracker/" . "event/" . $args['eventUid'], 'event' => json_encode($event === null ? "": $event), 'tracks' => json_encode($tracks)
        ]);
    }

    //Resultat på text BRM2021
    public function getResultList(ServerRequestInterface $request, ResponseInterface $response, $args){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');
        $year = $route->getArgument('year');
         $result =  $this->resultService->resultsOnEvent($eventUid,$year);
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }

    public function track(ServerRequestInterface $request, ResponseInterface $response, $args){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $eventUid = $route->getArgument('eventUid');

        $tracks =  $this->trackService->tracksForEvent( "",$eventUid);
//        $year = $route->getArgument('year');
         $result =  $this->resultService->trackContestants($eventUid,$tracks);
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }


    public function trackrandonneurontrack(ServerRequestInterface $request, ResponseInterface $response, $args){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $trackUid = $route->getArgument('trackUid');
        $competitorUid = $route->getArgument('trackUid');
        $result =  $this->resultService->trackRandonneurOnTrack( $competitorUid,$trackUid);
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }


    public function resultForContestant(ServerRequestInterface $request, ResponseInterface $response, $args){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $competitorUId = $route->getArgument("uid");
        $params = $request->getQueryParams();


        if(array_key_exists('trackUid', $params)){
            $trackUid = $params["trackUid"];
        } else {
            $trackUid = "";
        }
        if(array_key_exists('eventUid', $params)){
            $eventUid = $params['eventUid'];
        } else {
            $eventUid = "";
        }


        $result =  $this->resultService->resultForContestant($competitorUId,$trackUid,$eventUid);
        $response->getBody()->write((string)json_encode($result), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    //Resultat på text BRM2021
    public function getResult(ServerRequestInterface $request, ResponseInterface $response, $args){
        $response->getBody()->write("<html><body>jjsadjsajd</body></html>");
        return $response->withHeader('Content-Type', 'text/html')->withStatus(200);

    }

    // tillfälligt testdata
    private function getJson(): array {
        return  [["ID" => '1042', "BRM" => '200K',"Efternam" => "Johansson", "Förnamn" => 'Kalle', 'Klubb' => 'Cykelintresset', "ACP-kod" => '113072', "Brevetnr" => '779642', "Tid" => '07:12' , "SR" => ''],
            ["ID" => '1042', "BRM" => '200K',"Efternam" => "Johanesson", "Förnamn" => 'Johanna', 'Klubb' => 'Gimonäs', "ACP-kod" => '113110', "Brevetnr" => '132746', "Tid" => '39:12' , "SR" => 'SR']];

    }

    //Resultat på text BRM2021
    public function getResultsPhp(ServerRequestInterface $request, ResponseInterface $response){
        $arr[] = [["name" => "Kalle", "kon" => "man", "track" => "Brm 300 Lycksele"], ["name" => "Mona", "kon" => "Kvinna","track" => "Brm 200 Bjurholm"]];
        $phpView = new PhpRenderer("../templates", ["title" => "My App"]);
        $phpView->setLayout("layout.php");
        $phpView->setAttributes(["resultlist" => $arr, "test" => 'Hej']);
        $phpView->render($response, "results.php", ["title" => "Hello - My App", "name" => "John"]);

        return $response;
    }

}