<?php

namespace App\Services\Geoquery;

use App\Entity\Commune;

class Geoquery{

	public function populate(Commune $commune, string $nom , string $code): ?Commune
	{
		$request = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$code."&nom=".$nom."&fields=nom,code,codesPostaux,centre,contour,codeDepartement,codeRegion,population&format=json&geometry=contour"))[0];

		$commune->setNom($nom);
		$commune->setCodeInsee($request->code);
		$commune->setCodesPostaux($request->codesPostaux);
		$commune->setCentre($request->centre->coordinates);
		$commune->setContour($request->contour->coordinates);
		$commune->setCodeDepartement($request->codeDepartement);
		$commune->setCodeRegion($request->codeRegion);

		return $commune;
	}

	public function populateArray(array $array, string $nom = null, string $code = null): ?array
	{
		if(!$array["nom"] && !$array["code"]){
			return $array;
		}

		$request = json_decode(file_get_contents("https://geo.api.gouv.fr/communes?codePostal=".$array["code"]."&nom=".$array["nom"]."&fields=nom,code,codesPostaux,centre,contour,codeDepartement,codeRegion,population&format=json&geometry=contour"))[0];

		$array["nom"] = $request->nom;
		$array["code_insee"] = $request->code;
		$array["codes_postaux"] = $request->codesPostaux;
		$array["centre"] = $request->centre->coordinates;
		$array["contour"] = $request->contour->coordinates;
		$array["code_departement"] = $request->codeDepartement;
		$array["code_region"] = $request->codeRegion;

		return $array;
	}
}



