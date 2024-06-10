<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $payment_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deposit_date = null;

    #[ORM\Column(nullable: true)]
    private ?float $total_priceHT = null;

    #[ORM\Column(nullable: true)]
    private ?float $total_priceTTC = null;

    #[ORM\ManyToOne(inversedBy: 'client_order')]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'employee_order')]
    private ?User $employee = null;

    #[ORM\OneToMany(mappedBy: 'orderlinecommand', targetEntity: CommandLine::class)]
    private Collection $commandLines;

    public function __toString()
    {
        return 'Order #' . $this->getId();
    }
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $pickup_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    public function __construct()
    {
        $this->commandLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeInterface $payment_date): static
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    public function getDepositDate(): ?\DateTimeInterface
    {
        return $this->deposit_date;
    }

    public function setDepositDate(?\DateTimeInterface $deposit_date): static
    {
        $this->deposit_date = $deposit_date;

        return $this;
    }

    public function getTotalPriceHT(): ?float
    {
        return $this->total_priceHT;
    }

    public function setTotalPriceHT(?float $total_priceHT): static
    {
        $this->total_priceHT = $total_priceHT;

        return $this;
    }

    public function getTotalPriceTTC(): ?float
    {
        return $this->total_priceTTC;
    }

    public function setTotalPriceTTC(?float $total_priceTTC): static
    {
        $this->total_priceTTC = $total_priceTTC;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * @return Collection<int, CommandLine>
     */
    public function getCommandLines(): Collection
    {
        return $this->commandLines;
    }

    public function addCommandLine(CommandLine $commandLine): static
    {
        if (!$this->commandLines->contains($commandLine)) {
            $this->commandLines->add($commandLine);
            $commandLine->setOrderlinecommand($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): static
    {
        if ($this->commandLines->removeElement($commandLine)) {
            // set the owning side to null (unless already changed)
            if ($commandLine->getOrderlinecommand() === $this) {
                $commandLine->setOrderlinecommand(null);
            }
        }

        return $this;
    }

    public function getPickupDate(): ?\DateTimeInterface
    {
        return $this->pickup_date;
    }

    public function setPickupDate(?\DateTimeInterface $pickup_date): static
    {
        $this->pickup_date = $pickup_date;

        return $this;
    }

    public function calculateTotalHT(): float
    {
        $totalHT = 0;

        foreach ($this->commandLines as $commandLine) {
            $totalHT += $commandLine->calculateTotalHT();
        }

        return $totalHT;
    }

    public function calculateTotalTTC(): float
    {
        $totalHT = $this->calculateTotalHT();
        $totalTTC = $totalHT * 1.10;
    
        return $totalTTC;
    }

    public function updateTotalTTC(EntityManagerInterface $entityManager): void
    {
        // calcul HT
        $totalHT = $this->calculateTotalHT();

        // Calcul TVA 10%
        $totalTTC = $totalHT * 1.10;

        // Set total TTC dans entité
        $this->setTotalPriceTTC($totalTTC);

        $entityManager->persist($this);
        $entityManager->flush();
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }
}
