<?php

namespace App\Entity;

use App\Repository\KanbanColumnIssueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KanbanColumnIssueRepository::class)]
class KanbanColumnIssue
{

    #[ORM\Column]
    private ?int $sequence = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'kanbanColumnIssues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?KanbanColumn $kanbanColumn = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'kanbanColumnIssues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Issue $issue = null;

    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getKanbanColumn(): ?KanbanColumn
    {
        return $this->kanbanColumn;
    }

    public function setKanbanColumn(?KanbanColumn $kanban_column): self
    {
        $this->kanbanColumn = $kanban_column;

        return $this;
    }

    public function getIssue(): ?Issue
    {
        return $this->issue;
    }

    public function setIssue(?Issue $issue): self
    {
        $this->issue = $issue;

        return $this;
    }
}