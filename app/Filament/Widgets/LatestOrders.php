<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Number;
use App\Models\Order;

class LatestOrders extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->searchable(),

                TextColumn::make('grand_total')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(function ($state, $record) {
                        $currency = $record->currency;
                        return Number::currency($state, $currency);
                    }),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state):string => match($state){
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn (string $state):string => match($state){
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                    })
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'text-transform: capitalize;'
                    ]),

                TextColumn::make('payment_method')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'style' => 'text-transform: capitalize;'
                    ]),
                
                TextColumn::make('payment_status')
                    ->sortable()
                    ->badge()
                    ->searchable()
                    ->extraAttributes([
                        'style' => 'text-transform: capitalize;'
                    ]),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->sortable()
                    ->dateTime()
            ])
            ->actions([
                Action::make('View Order')
                    ->url(fn (Order $record):string => OrderResource::getUrl('view', [
                        'record' => $record
                    ]))
                    ->icon('heroicon-o-eye')
            ]);
    }
}
