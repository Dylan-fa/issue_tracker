<?php

namespace App\Entity;

use App\Repository\KanbanColumnRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KanbanColumnRepository::class)]
class KanbanColumn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'columns')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\OneToMany(mappedBy: 'kanbanColumn', targetEntity: KanbanColumnIssue::class)]
    private Collection $kanbanColumnIssues;

    public function __construct()
    {
        $this->kanbanColumnIssues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getKanbanColumnIssues(): Collection
    {
        return $this->kanbanColumnIssues;
    }

    public function addKanbanColumnIssue(KanbanColumnIssue $kanbanColumnIssue): self
    {
        if (!$this->kanbanColumnIssues->contains($kanbanColumnIssue)) {
            $this->kanbanColumnIssues->add($kanbanColumnIssue);
            $kanbanColumnIssue->setKanbanColumn($this);
        }

        return $this;
    }

    public function removeKanbanColumnIssue(KanbanColumnIssue $kanbanColumnIssue): self
    {
        if ($this->kanbanColumnIssues->removeElement($kanbanColumnIssue)) {
            if ($kanbanColumnIssue->getKanbanColumn() === $this) {
                $kanbanColumnIssue->setKanbanColumn(null);
            }
        }

        return $this;
    }
}