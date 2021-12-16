<?php
	
	namespace App\Utilities;
	
	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Bridge\Twig\Mime\TemplatedEmail;
	use Symfony\Component\Mailer\MailerInterface;
	use Symfony\Component\Mime\Address;
	
	class GestionMail
	{
		private $mailer;
		private $_em;
		
		public function __construct(MailerInterface $mailer, EntityManagerInterface $_em)
		{
			$this->mailer = $mailer;
			$this->_em = $_em;
		}
		
		/**
		 * @param $candidat
		 * @return bool
		 * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
		 */
		public function candidature($candidat)
		{
			$email = (new TemplatedEmail())
				->from(new Address('conafor@scoutascci.org', 'ASCCI-CONAFOR'))
				->to(new Address($candidat->getCandidat()->getEmail(),$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms()))
				->addCc(new Address('delrodieamoikon@gmail.com'), new Address('conafor.ascci@gmail.com'))
				->subject('VALIDATION  DE LA CANDIDATURE DE '.$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms())
				->htmlTemplate('email/validation.html.twig')
				->context([
					'candidat' => $candidat
				])
				;
			$this->mailer->send($email);
			return true;
		}
		
		/**
		 * @param $candidater
		 * @return bool
		 * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
		 */
		public function demande($candidater)
		{
			$email = (new TemplatedEmail())
				->from(new Address('conafor@scoutascci.org', 'ASCCI-CONAFOR'))
				->to(...$this->administrateur())
				->addCc(new Address('delrodieamoikon@gmail.com'), new Address('conafor.ascci@gmail.com'))
				->subject('CANDIDATURE DE '.$candidater->getCandidat()->getNom().' '.$candidater->getCandidat()->getPrenoms())
				->htmlTemplate('email/demande.html.twig')
				->context([
					'candidater' => $candidater
				])
			;
			$this->mailer->send($email);
			
			return true;
		}
		
		public function paiement($candidater)
		{
			$email = (new TemplatedEmail())
				->from(new Address('conafor@scoutascci.org', 'ASCCI-CONAFOR'))
				->to(...$this->administrateur())
				->addCc(new Address('delrodieamoikon@gmail.com'), new Address('conafor.ascci@gmail.com'))
				->subject('CANDIDATURE DE '.$candidater->getCandidat()->getNom().' '.$candidater->getCandidat()->getPrenoms())
				->htmlTemplate('email/paiement.html.twig')
				->context([
					'candidat' => $candidater
				])
			;
			$this->mailer->send($email);
			
			return true;
		}
		
		/**
		 * @param $candidat
		 * @return bool
		 * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
		 */
		public function rejet($candidat): bool
		{
			$email = (new TemplatedEmail())
				->from(new Address('conafor@scoutascci.org', 'ASCCI-CONAFOR'))
				->to(...$this->administrateur())
				->addCc(new Address('delrodieamoikon@gmail.com'), new Address('conafor.ascci@gmail.com'))
				->subject('REJET DE  LA CANDIDATURE DE '.$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms())
				->htmlTemplate('email/rejet.html.twig')
				->context([
					'candidat' => $candidat
				])
			;
			$this->mailer->send($email);
			
			$email_admin = (new TemplatedEmail())
				->from(new Address('conafor@scoutascci.org', 'ASCCI-CONAFOR'))
				->to(new Address($candidat->getCandidat()->getEmail(),$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms()))
				->addCc(new Address('delrodieamoikon@gmail.com'), new Address('conafor.ascci@gmail.com'))
				->subject('REJET DE  LA CANDIDATURE DE '.$candidat->getCandidat()->getNom().' '.$candidat->getCandidat()->getPrenoms())
				->htmlTemplate('email/rejet_admin.html.twig')
				->context([
					'candidat' => $candidat
				])
			;
			$this->mailer->send($email_admin);
			
			return true;
		}
		
		/**
		 * @return array
		 */
		protected function administrateur(): array
		{
			$users = $this->_em->getRepository(User::class)->findAll();
			$admin=[];$i=0;
			foreach ($users as $user){
				if ($user->getRoles()[1]=== 'ROLE_ADMIN'){
					$admin[$i++]= $user->getEmail();
				}
			}
			//dd($admin);
			
			return $admin;
		}
	}
