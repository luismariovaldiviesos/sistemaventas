<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\OrderDetail;
use DateTime;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{

    public $year, $salesByMonth_Data = [], $top5Data =[], $weekSales_Data=[], $listYears=[] ;

    public function mount()
    {
        if ($this->year == '') $this->year = date('Y');
    }


    public function render()
    {

        $this->listYears =[];
        $currentYear =  date('Y') -1;
        for ($i=1; $i < 7 ; $i++) {
            array_push($this->listYears, $currentYear +$i);
        }

        $this->getTop5();
        $this->getWeekSales();
        $this->getSalesMonth();

        return view('livewire.dash.component')->layout('layouts.theme.app');
    }

    public function getTop5 ()
    {

        $this->top5Data = OrderDetail::join('products as p', 'order_details.product_id','p.id')
        ->select(
            DB::raw("p.name as product, sum(order_details.quantity * p.price) as total")
        )->whereYear('order_details.created_at', $this->year)
        ->groupBy('p.name')
        ->orderBy(DB::raw("sum(order_details.quantity * p.price)"), 'desc')
        ->get()->take(5)->toArray();

        $contDif = ( 5 - count($this->top5Data)); // si la consulta devuelve 5 productos o menos

        if ($contDif > 0) {
            for ($i=1; $i <= $contDif; $i++) {
                array_push($this->top5Data, ["product" => "-", "total" => 0]);
            }
        }

    }

    public function getWeekSales()
    {
        $dt = new DateTime();
        $dates = [];
        $startDate = null;
        $finishDate = null;
        $this->weekSales_Data = [];

        for ($d = 1; $d <= 7; $d++) {

            $dt->setISODate($dt->format('o'), $dt->format('W'), $d);
            $dates[$dt->format('dd')] = $dt->format('m-d-Y');

            $startDate = $dt->format('Y-m-d') . ' 00:00:00';
            $finishDate = $dt->format('Y-m-d') . ' 23:59:59';

            //replace year
            $startDate = substr_replace($startDate, $this->year, 0, 4);
            $finishDate = substr_replace($finishDate, $this->year, 0, 4);


            $wsale = Order::whereBetween('created_at', [$startDate, $finishDate])->sum('total');

            array_push($this->weekSales_Data, $wsale);

        }

    }


    public function getSalesMonth()
    {
        $this->salesByMonth_Data = [];

        $salesByMonth = DB::select(
            DB::raw("SELECT coalesce(total,0)as total
                FROM (SELECT 'january' AS month UNION SELECT 'february' AS month UNION SELECT 'march' AS month UNION SELECT 'april' AS month UNION SELECT 'may' AS month UNION SELECT 'june' AS month UNION SELECT 'july' AS month UNION SELECT 'august' AS month UNION SELECT 'september' AS month UNION SELECT 'october' AS month UNION SELECT 'november' AS month UNION SELECT 'december' AS month ) m LEFT JOIN (SELECT MONTHNAME(created_at) AS MONTH, COUNT(*) AS orders, SUM(total)AS total
                FROM orders WHERE year(created_at)= $this->year
                GROUP BY MONTHNAME(created_at),MONTH(created_at)
                ORDER BY MONTH(created_at)) c ON m.MONTH =c.MONTH;")
        );

        foreach ($salesByMonth as $sale) {
            array_push($this->salesByMonth_Data, $sale->total);
        }

    }

    // ecuchar cuadno la propiedad year se actualice
    public function updatedYear()
    {
        $this->dispatchBrowserEvent('reload-scripts');
    }
}
