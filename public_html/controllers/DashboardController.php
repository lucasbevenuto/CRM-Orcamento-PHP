<?php

class DashboardController extends Controller
{
    private Quote $quotes;
    private Client $clients;
    private Product $products;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->quotes = new Quote();
        $this->clients = new Client();
        $this->products = new Product();
    }

    public function index(): void
    {
        $summary = $this->quotes->dashboardSummary();
        $monthly = $this->quotes->monthlyRevenueSeries(6);
        $clientGrowth = $this->clients->monthlyRegistrations(6);

        $this->render('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'stats' => $this->quotes->stats(),
            'summary' => $summary,
            'recentQuotes' => array_slice($this->quotes->all(), 0, 5),
            'clientCount' => count($this->clients->all()),
            'productCount' => count($this->products->all()),
            'monthlyData' => $monthly,
            'clientGrowth' => $this->buildClientGrowthSeries($monthly['labels'], $clientGrowth),
            'topClients' => $this->quotes->topClients(5),
            'statusBreakdown' => $this->quotes->statusBreakdown(),
        ]);
    }

    private function buildClientGrowthSeries(array $labels, array $clientGrowthMap): array
    {
        $series = [];

        foreach ($labels as $label) {
            $month = DateTime::createFromFormat('M/y', $label);
            $periodKey = $month ? $month->format('Y-m') : '';
            $series[] = $clientGrowthMap[$periodKey] ?? 0;
        }

        return $series;
    }
}
