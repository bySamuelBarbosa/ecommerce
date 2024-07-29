<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {

        $currencyWithMostSoldProducts = Order::query()
            ->select('currency', DB::raw('SUM(grand_total) as total_grand_total'))
            ->groupBy('currency')
            ->orderByDesc('total_grand_total')
            ->first()
            ->currency;

        
        $averagePrice = Order::query()
            ->where('currency', $currencyWithMostSoldProducts)
            ->avg('grand_total');


        return [
            Stat::make('New Orders', Order::query()->where('status', 'new')->count()),
            Stat::make('Order Processing', Order::query()->where('status', 'processing')->count()),
            Stat::make('Order Shipped', Order::query()->where('status', 'shipped')->count()),
            Stat::make('Order Delivered', Order::query()->where('status', 'delivered')->count()),
            Stat::make('Order Cancelled', Order::query()->where('status', 'cancelled')->count()),
            Stat::make('Average Price', Number::currency($averagePrice, $currencyWithMostSoldProducts)),
        ];
    }
}
