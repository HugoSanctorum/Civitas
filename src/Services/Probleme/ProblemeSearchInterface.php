<?php

namespace App\Services\Probleme;

use App\Repository\CategorieRepository;
use App\Repository\StatutRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProblemeSearchInterface{

    private $categorieRepository;
    private $statutRepository;
    private $session;

    public function __construct(
        CategorieRepository $categorieRepository,
        StatutRepository $statutRepository,
        SessionInterface $session
    ){
        $this->categorieRepository = $categorieRepository;
        $this->statutRepository = $statutRepository;
        $this->session = $session;
    }

    public function clearSession(){
        $this->session->remove("search_nom_probleme");
        $this->session->remove("search_categories");
        $this->session->remove("search_statuts");
        $this->session->remove("search_element");
        $this->session->remove("search_orderBy");
    }

    public function searchInApp(array $query)
    {
        if (array_key_exists("tri", $query)) {
            if ($query['tri'] == true){
                $this->clearSession();
                foreach($query as $param => $value){
                    switch ($param) {
                        case 'nom':
                            $this->session->set("search_nom_probleme", $value);
                            break;
                        case 'categories':
                            $liste = explode(",", $value);
                            $categories = [];
                            foreach ($liste as $nom) {
                                $request = $this->categorieRepository->findOneBy(['nom' => $nom]);
                                if ($request) array_push($categories, $request->getId());
                            }
                            $this->session->set("search_categories", $categories);
                            break;
                        case 'statuts':
                            $liste = explode(",", $value);
                            $statuts = [];
                            foreach ($liste as $nom) {
                                $request = $this->statutRepository->findOneBy(['nom' => $nom]);
                                if ($request) array_push($statuts, $request->getId());
                            }
                            $this->session->set("search_statuts", $statuts);
                            break;
                        case 'element':
                            if(is_numeric($value)){
                                $value = intval($value);
                                if($value > 0 && $value % 1 == 0){
                                    $this->session->set("search_element", $value);
                                }
                            }
                            break;
                        case 'orderBy':
                            if (in_array($value, ["date_asc", "date_desc", "priorite", "categorie", "statut"]))
                                $this->session->set("search_orderby", $value);
                            break;
                    } 
                }
            }
        }
    }

    public function searchToArray(array $query): Array
    {
        $params = [
            "nom" => "",
            "categories" => [],
            "statuts" => [],
            "element" => 20,
            "orderBy" => "priorite"
        ];

        foreach($query as $param => $value){
            switch ($param) {
                case 'nom':
                    $params["nom"] = $value;
                    break;
                case 'categories':
                    $liste = explode(",", $value);
                    $categories = [];
                    foreach ($liste as $nom) {
                        $request = $this->categorieRepository->findOneBy(['nom' => $nom]);
                        if ($request) array_push($categories, $request->getId());
                    }
                    $params["categories"] = $categories;
                    break;
                case 'statuts':
                    $liste = explode(",", $value);
                    $statuts = [];
                    foreach ($liste as $nom) {
                        $request = $this->statutRepository->findOneBy(['nom' => $nom]);
                        if ($request) array_push($statuts, $request->getId());
                    }
                    $params["statuts"] = $statuts;
                    break;
                case 'element':
                    if(is_numeric($value)){
                        $value = intval($value);
                        if($value > 0 && $value % 1 == 0){
                            $params["element"] = $value;
                        }
                    }
                    break;
                case 'orderBy':
                    if (in_array($value, ["date_asc", "date_desc", "priorite", "categorie", "statut"]))
                        $params["orderBy"] = $value;
                    break;
            } 
        }
        return $params;
    }
}