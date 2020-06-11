<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\ProblemeRepository;

use App\Services\Probleme\ProblemeSearchInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;




/**
 * @Route("/rest")
 */
class RestController extends AbstractController
{
	private $encoders;
	private $normalizers;

	public function __construct(){
		$this->encoders = [new JsonEncoder()];
		$this->normalizers = [new DateTimeNormalizer('d/m/Y'), new ObjectNormalizer()];
	}

	/**
     *@Route("/probleme", name="rest_probleme", methods={"GET"})
     */
	public function getProbleme(
		Request $request,
		ProblemeRepository $problemeRepository,
		ProblemeSearchInterface $problemeSearchInterface
	): JsonResponse
	{
		$params = $problemeSearchInterface->searchToArray($request->query->all());

		$problemes = $problemeRepository->findPaginateByCategoryAndName(
			1,
			$params["element"],
			$params["categories"],
			$params["statuts"],
			$params["nom"],
			$params["orderBy"]
		);
		
		$serializer = new Serializer($this->normalizers, $this->encoders);
		$data = [];

		foreach($problemes as $probleme){
			$jsonValue = $serializer->normalize(
				$probleme,
			 	'json',
			 	[AbstractNormalizer::ATTRIBUTES => [
			 		'id',
			 		'titre',
			 		'description',
			 		'localisation',
			 		'reference',
			 		'Commune' => ['nom', 'codesPostaux', 'codeRegion', 'codeDepartement', 'codeInsee'],
			 		'Categorie' => ['nom'],
			 		'Priorite' => ['nom', 'poids'],
			 		'HistoriqueStatuts' => ['date', 'description', 'Statut' => ['nom']]
			 	]]
			 );
			array_push($data, $jsonValue);
		}

		return new JsonResponse($data);
	}
}