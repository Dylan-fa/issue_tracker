<?php

namespace App\Controller;

use App\Repository\IssueAttributeRepositoryInterface;
use App\Repository\PriorityRepository;
use App\Repository\StatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * This controller handles the resources for site statistics and uses Symfony UX Chartjs library
 */
class StatisticsController extends AbstractController
{
    private function generateIssueAttributePieChart(IssueAttributeRepositoryInterface $repository, ChartBuilderInterface $chartBuilder, string $name): Chart
    {
        $result = $repository->findNumberOfUses();
        
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $chart->setData([
            'labels' => array_column($result, 'name'),
            'datasets' => [
                [
                    'backgroundColor' => array_column($result, 'color'),
                    'data' => array_column($result, 'number'),
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Issue ' . $name . ' Pie chart'
                ],
            ]
        ]);
        
        return $chart;
    }


    #[Route('/statistics', name: 'app_statistics')]
    public function index(ChartBuilderInterface $chartBuilder, StatusRepository $statusRepository, PriorityRepository $priorityRepository): Response
    {
        $priorityChart = $this->generateIssueAttributePieChart($priorityRepository, $chartBuilder, 'Priority');
        $statusChart = $this->generateIssueAttributePieChart($statusRepository, $chartBuilder, 'Status');

        return $this->render('statistics/index.html.twig', [
            'priority_chart' => $priorityChart,
            'status_chart' => $statusChart,
        ]);
    }
}