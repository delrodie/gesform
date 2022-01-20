<?php

namespace App\Entity;

use App\Repository\CandidaterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidaterRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Candidater
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $validation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idTransaction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statusPaiement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responseId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idOperateur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payementDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class)
     */
    private $candidat;

    /**
     * @ORM\ManyToOne(targetEntity=Activite::class)
     */
    private $activite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mention;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $acompte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token_acompte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_acompte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $responseId_acompte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getValidation(): ?bool
    {
        return $this->validation;
    }

    public function setValidation(?bool $validation): self
    {
        $this->validation = $validation;

        return $this;
    }

    public function getIdTransaction(): ?string
    {
        return $this->idTransaction;
    }

    public function setIdTransaction(?string $idTransaction): self
    {
        $this->idTransaction = $idTransaction;

        return $this;
    }

    public function getStatusPaiement(): ?string
    {
        return $this->statusPaiement;
    }

    public function setStatusPaiement(?string $statusPaiement): self
    {
        $this->statusPaiement = $statusPaiement;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getResponseId(): ?string
    {
        return $this->responseId;
    }

    public function setResponseId(?string $responseId): self
    {
        $this->responseId = $responseId;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getIdOperateur(): ?string
    {
        return $this->idOperateur;
    }

    public function setIdOperateur(?string $idOperateur): self
    {
        $this->idOperateur = $idOperateur;

        return $this;
    }

    public function getPayementDate(): ?string
    {
        return $this->payementDate;
    }

    public function setPayementDate(?string $payementDate): self
    {
        $this->payementDate = $payementDate;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
	
	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedAtValue(): \DateTime
                                        {
                                           return $this->createdAt = new \DateTime();
                                        }
	
	/**
	 * @ORM\PreUpdate
	 */
	public function setUpdatedAtValue(): \DateTime
                                    	{
                                    		return $this->updatedAt = new \DateTime();
                                    	}

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getActivite(): ?Activite
    {
        return $this->activite;
    }

    public function setActivite(?Activite $activite): self
    {
        $this->activite = $activite;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): self
    {
        $this->mention = $mention;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAcompte(): ?int
    {
        return $this->acompte;
    }

    public function setAcompte(?int $acompte): self
    {
        $this->acompte = $acompte;

        return $this;
    }

    public function getTokenAcompte(): ?string
    {
        return $this->token_acompte;
    }

    public function setTokenAcompte(?string $token_acompte): self
    {
        $this->token_acompte = $token_acompte;

        return $this;
    }

    public function getUrlAcompte(): ?string
    {
        return $this->url_acompte;
    }

    public function setUrlAcompte(?string $url_acompte): self
    {
        $this->url_acompte = $url_acompte;

        return $this;
    }

    public function getResponseIdAcompte(): ?string
    {
        return $this->responseId_acompte;
    }

    public function setResponseIdAcompte(string $responseId_acompte): self
    {
        $this->responseId_acompte = $responseId_acompte;

        return $this;
    }
}
