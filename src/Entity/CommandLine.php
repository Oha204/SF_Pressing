<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CommandLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandLineRepository::class)]
#[ApiResource]
class CommandLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $priceHT = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Order $orderlinecommand = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    private ?ArticleState $State = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriceHT(): ?float
    {
        return $this->priceHT;
    }

    public function setPriceHT(?float $priceHT): static
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;
    
        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;
    
        return $this;
    }

    public function getOrderlinecommand(): ?Order
    {
        return $this->orderlinecommand;
    }

    public function setOrderlinecommand(?Order $orderlinecommand): static
    {
        $this->orderlinecommand = $orderlinecommand;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): static
    {
        $this->material = $material;
    
        return $this;
    }

    public function getState(): ?ArticleState
    {
        return $this->State;
    }

    public function setState(?ArticleState $State): static
    {
        $this->State = $State;

        return $this;
    }

    public function calculateTotalHT(): float
    {
        $totalHT = 0;

        if ($this->service) {
            $totalHT += $this->service->getPrice();
        }

        if ($this->material) {
            $totalHT += $this->material->getPrice();
        }

        if ($this->article && $this->quantity) {
            $totalHT += $this->article->getPrice() * $this->quantity;
        }

        return $totalHT;
    }
}
