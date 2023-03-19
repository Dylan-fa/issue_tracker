<?php

namespace App\Entity;

use App\Repository\IssueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
#[ORM\Index(name: 'issue_fulltext', columns: ['title', 'description'], flags: ['fulltext'])]
class Issue
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Constraints\NotBlank]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'issues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Priority $priority = null;

    #[ORM\ManyToOne(inversedBy: 'assignedIssues')]
    private ?User $assignee = null;

    #[ORM\OneToMany(mappedBy: 'issue', targetEntity: Comment::class, cascade: ['remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'issue', targetEntity: KanbanColumnIssue::class)]
    private Collection $kanbanColumnIssues;

    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'issues')]
    private Collection $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->kanbanColumnIssues = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
    
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(?Priority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): self
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setIssue($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getIssue() === $this) {
                $comment->setIssue(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, KanbanColumnIssue>
     */
    public function getKanbanColumnIssues(): Collection
    {
        return $this->kanbanColumnIssues;
    }

    public function addKanbanColumnIssue(KanbanColumnIssue $kanbanColumnIssue): self
    {
        if (!$this->kanbanColumnIssues->contains($kanbanColumnIssue)) {
            $this->kanbanColumnIssues->add($kanbanColumnIssue);
            $kanbanColumnIssue->setIssue($this);
        }

        return $this;
    }

    public function removeKanbanColumnIssue(KanbanColumnIssue $kanbanColumnIssue): self
    {
        if ($this->kanbanColumnIssues->removeElement($kanbanColumnIssue)) {
            // set the owning side to null (unless already changed)
            if ($kanbanColumnIssue->getIssue() === $this) {
                $kanbanColumnIssue->setIssue(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Project $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Project $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}