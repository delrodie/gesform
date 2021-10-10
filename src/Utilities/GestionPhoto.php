<?php
	
	namespace App\Utilities;
	
	use Cocur\Slugify\Slugify;
	use Symfony\Component\HttpFoundation\File\Exception\FileException;
	use Symfony\Component\HttpFoundation\File\UploadedFile;
	
	class GestionPhoto
	{
		private $photo;
		private $formation;
		
		public function __construct($photoDirectory, $formationDirectory)
		{
			$this->photo = $photoDirectory;
			$this->formation = $formationDirectory;
		}
		
		/**
		 * Enregistrement du fichier dans le repertoire approprié
		 *
		 * @param UploadedFile $file
		 * @param null $media
		 * @return string
		 */
		public function upload(UploadedFile $file, $media = null): string
		{
			// Initialisation du slug
			$slugify = new Slugify(); //dd($file);
			
			$originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
			$safeFilename = $slugify->slugify($originalFileName);
			$newFilename = $safeFilename.'-'.Time().'.'.$file->guessExtension(); //dd($this->mediaActivite);
			
			// Deplacement du fichier dans le repertoire dedié
			try {
				if ($media === 'photo') $file->move($this->photo, $newFilename);
				elseif ($media === 'media') $file->move($this->formation, $newFilename);
				else $file->move($this->photo, $newFilename);
			}catch (FileException $e){
			
			}
			
			return $newFilename;
		}
		
		/**
		 * Suppression de l'ancien media sur le server
		 *
		 * @param $ancienMedia
		 * @param null $media
		 * @return bool
		 */
		public function removeUpload($ancienMedia, $media = null): bool
		{
			if ($media === 'photo') unlink($this->photo.'/'.$ancienMedia);
			elseif ($media === 'media') unlink($this->formation.'/'.$ancienMedia);
			else return false;
			
			return true;
		}
	}
