<?php

namespace App\Repository;
/**
 * Interface represents an issue attribute repository with a finite number of options e.g., status or priority
 */
Interface IssueAttributeRepositoryInterface 
{
    public function findNumberOfUses();
}