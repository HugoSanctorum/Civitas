<?php

namespace App\Services\Geoquery;

use App\Entity\Commune;

class Geoquery{

	public function populate(Commune $commune, string $nom, string $code): ?Commune
	{
		$request = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$code."&nom=".$nom."&fields=nom,code,codesPostaux,centre,contour,codeDepartement,codeRegion,population&format=json&geometry=contour"))[0];

		/*if(empty($request)) dd($nom, $code);
		else $request = $request[0];*/

		$commune->setNom($request->nom);
		$commune->setCodeInsee($request->code);
		$commune->setCodesPostaux($request->codesPostaux);
		$commune->setCentre($request->centre->coordinates);
		$commune->setContour($request->contour->coordinates);
		$commune->setCodeDepartement($request->codeDepartement);
		$commune->setCodeRegion($request->codeRegion);

		return $commune;
	}
}



