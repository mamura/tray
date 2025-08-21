<?php

namespace Tests\Unit\Observers;

use App\Models\Sale;
use App\Observers\SaleObserver;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class SaleObserverTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_created_uses_tags_flush_for_date_and_seller(): void
    {
        $date = '2025-01-10';
        $sid  = 42;

        $store = \Mockery::mock(\Illuminate\Cache\TaggableStore::class);
        Cache::shouldReceive('getStore')->andReturn($store);

        $taggedDate   = \Mockery::mock(); $taggedDate->shouldReceive('flush')->once();
        $taggedSeller = \Mockery::mock(); $taggedSeller->shouldReceive('flush')->once();

        Cache::shouldReceive('tags')->once()->with(['reports', "date:{$date}"])->andReturn($taggedDate);
        Cache::shouldReceive('tags')->once()->with(['reports', "seller:{$sid}"])->andReturn($taggedSeller);

        $sale = new \App\Models\Sale();
        $sale->seller_id = $sid;
        $sale->sold_at   = $date;

        (new \App\Observers\SaleObserver())->created($sale);

        $this->addToAssertionCount(1);
    }

    public function test_updated_uses_tags_for_old_and_new_pairs(): void
    {
        $oldDate   = '2025-01-09';
        $newDate   = '2025-01-10';
        $oldSeller = 10;
        $newSeller = 11;

        $store = \Mockery::mock(\Illuminate\Cache\TaggableStore::class);
        Cache::shouldReceive('getStore')->andReturn($store);

        $mDateOld    = \Mockery::mock(); $mDateOld->shouldReceive('flush')->once();
        $mDateNew    = \Mockery::mock(); $mDateNew->shouldReceive('flush')->once();
        $mSellerOld  = \Mockery::mock(); $mSellerOld->shouldReceive('flush')->once();
        $mSellerNew  = \Mockery::mock(); $mSellerNew->shouldReceive('flush')->once();

        Cache::shouldReceive('tags')->once()->with(['reports', "date:{$newDate}"])->andReturn($mDateNew);
        Cache::shouldReceive('tags')->once()->with(['reports', "seller:{$newSeller}"])->andReturn($mSellerNew);
        Cache::shouldReceive('tags')->once()->with(['reports', "date:{$oldDate}"])->andReturn($mDateOld);
        Cache::shouldReceive('tags')->once()->with(['reports', "seller:{$oldSeller}"])->andReturn($mSellerOld);

        $sale = new \App\Models\Sale();
        $sale->setRawAttributes(['seller_id' => $oldSeller, 'sold_at' => $oldDate], true);
        $sale->seller_id = $newSeller;
        $sale->sold_at   = $newDate;

        (new \App\Observers\SaleObserver())->updated($sale);

        $this->addToAssertionCount(1);
    }

}
