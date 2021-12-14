<?php
	
	namespace App\Utilities;
	
	use Symfony\Bridge\Twig\Mime\TemplatedEmail;
	use Symfony\Component\Mailer\MailerInterface;
	use Symfony\Component\Mime\Address;
	
	class GestionMail
	{
		private $mailer;
		
		public function __construct(MailerInterface $mailer)
		{
			$this->mailer = $mailer;
		}
		
		public function candidature($candidat)
		{
			$email = (new TemplatedEmail())
				->from(new Address('conafor@scoutascci.org', 'ASCCI-CONAFOR'))
				//->to(new Address($candidat->getCandidat()->getEmail(),$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms()))
				->addCc(new Address('delrodieamoikon@gmail.com',$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms()))
				->subject('VALIDATION CANDIDATURE FORMATION')
				->htmlTemplate('email/validation.html.twig')
				->context([
					'candidat' => $candidat
				])
				;
			$this->mailer->send($email);
		}
	}
