<?php

namespace App\Controller;

use App\Domain\Model\CheckPoint\Checkpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use Slim\Views\Twig;

class ResultsController
{



    //Resultat på text BRM2021
    public function getResultView(ServerRequestInterface $request, ResponseInterface $response, $args){
        $view = Twig::fromRequest($request);
        return $view->render($response, 'result.html', [
            'link' => "/api/resultList/year/" . strval($args['year']) . "/event/" . $args['eventUid']
        ]);
    }

    //Resultat på text BRM2021
    public function getResultList(ServerRequestInterface $request, ResponseInterface $response, $args){
        $response->getBody()->write((string)json_encode($this->getJson()), JSON_UNESCAPED_SLASHES);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    }
    // tillfälligt testdata
    private function getJson(): array {
        return  [["ID" => '1042', "BRM" => '200K',"Efternam" => "Johansson", "Förnamn" => 'Kalle', 'Klubb' => 'Cykelintresset', "ACP-kod" => '113072', "Brevetnr" => '779642', "Tid" => '07:12' , "SR" => ''],
            ["ID" => '1042', "BRM" => '200K',"Efternam" => "Johanesson", "Förnamn" => 'Johanna', 'Klubb' => 'Gimonäs', "ACP-kod" => '113110', "Brevetnr" => '132746', "Tid" => '39:12' , "SR" => 'SR']];

    }

    //Resultat på text BRM2021
    public function getResultsPhp(ServerRequestInterface $request, ResponseInterface $response){
        $arr[] = [["name" => "Kalle", "kon" => "man"], ["name" => "Mona", "kon" => "Kvinna"]];
        $phpView = new PhpRenderer("../templates", ["title" => "My App"]);
        $phpView->setLayout("layout.php");
        $phpView->setAttributes(["resultlist" => $arr, "test" => 'Hej']);
        $phpView->render($response, "results.php", ["title" => "Hello - My App", "name" => "John"]);

        return $response;
    }

}