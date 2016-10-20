<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Created by PhpStorm.
 * User: ivan.li
 * Date: 10/19/2016
 * Time: 5:22 PM
 */
class SubscriptionRepoTest extends TestCase
{
    use DatabaseTransactions;
    protected $subscriptionRepo;

    public function setUp()
    {
        parent::setUp();
        Session::start();
        $this->subscriptionRepo = app(\App\Contracts\Repository\Subscription\SubscriptionContract::class);
    }

    public function testGetProductFamilies()
    {
        $productFamilies = $this->subscriptionRepo->getProductFamilies();
        $this->assertTrue(is_array($productFamilies) && !empty($productFamilies));
        foreach ($productFamilies as $productFamily) {
            $this->assertTrue(isset($productFamily->product_family));
            $this->assertTrue(is_object($productFamily->product_family));
            $this->assertTrue(isset($productFamily->product_family->id));
        }
    }

    public function testGetProductsByProductFamily()
    {
        $productFamilies = $this->subscriptionRepo->getProductFamilies();
        foreach ($productFamilies as $productFamily) {
            $products = $this->subscriptionRepo->getProductsByProductFamily($productFamily->product_family->id);
            $this->assertTrue(is_array($products) && !empty($products));
            foreach ($products as $product) {
                $this->assertTrue(isset($product->product));
                $this->assertTrue(is_object($product->product));
                $this->assertTrue(isset($product->product->id));
            }
        }
    }

    public function testGetComponentsByProductFamily()
    {
        $productFamilies = $this->subscriptionRepo->getProductFamilies();
        foreach ($productFamilies as $productFamily) {
            $components = $this->subscriptionRepo->getComponentsByProductFamily($productFamily->product_family->id);
            $this->assertTrue(is_array($components));
            $this->assertTrue(!empty($components));
            foreach ($components as $component) {
                $this->assertTrue(isset($component->component));
                $this->assertTrue(is_object($component->component));
                $this->assertTrue(isset($component->component->prices));
                $this->assertTrue(is_array($component->component->prices));
                foreach ($component->component->prices as $price) {
                    $this->assertTrue(isset($price->starting_quantity) || is_null($price->starting_quantity));
                    $this->assertTrue(is_int($price->starting_quantity) || is_null($price->starting_quantity));
                    $this->assertTrue(isset($price->ending_quantity) || is_null($price->ending_quantity));
                    $this->assertTrue(is_int($price->ending_quantity) || is_null($price->ending_quantity));
                }
            }
        }
    }

    public function testGetProducts()
    {
        $products = $this->subscriptionRepo->getProducts();
        $this->assertTrue(is_array($products) && !empty($products));
        foreach ($products as $product) {
            $this->assertTrue(isset($product->product));
            $this->assertTrue(is_object($product->product));
            $this->assertTrue(isset($product->product->id));
        }
    }

    public function testGetProductById()
    {
        $products = $this->subscriptionRepo->getProducts();
        $this->assertTrue(is_array($products) && !empty($products));
        foreach ($products as $product) {
            $this->assertTrue(isset($product->product));
            $this->assertTrue(is_object($product->product));
            $this->assertTrue(isset($product->product->id));
            $targetProduct = $this->subscriptionRepo->getProduct($product->product->id);
            $this->assertTrue(!is_null($targetProduct));
            $this->assertTrue(is_object($targetProduct));
            $this->assertTrue(isset($targetProduct->id));
        }
    }

    public function testGetSubscriptions()
    {
        $subscriptions = $this->subscriptionRepo->getSubscriptions();
        $this->assertTrue(is_array($subscriptions));
    }

    public function testGetSubscription()
    {
        $subscriptions = $this->subscriptionRepo->getSubscriptions();
        $this->assertTrue(is_array($subscriptions));
        foreach ($subscriptions as $subscription) {
            $this->assertTrue(isset($subscription->subscription));
            $this->assertTrue(is_object($subscription->subscription));
            $this->assertTrue(isset($subscription->subscription->id));
            $targetSubscription = $this->subscriptionRepo->getSubscription($subscription->subscription->id);
            $this->assertTrue(!is_null($targetSubscription));
            $this->assertTrue(is_object($targetSubscription));
            $this->assertTrue(isset($targetSubscription->id));
        }
    }

    public function testGetTransactions()
    {
        $subscriptions = $this->subscriptionRepo->getSubscriptions();
        $this->assertTrue(is_array($subscriptions));
        foreach($subscriptions as $subscription){
            $this->assertTrue(isset($subscription->subscription));
            $this->assertTrue(is_object($subscription->subscription));
            $this->assertTrue(isset($subscription->subscription->id));
            $transactions = $this->subscriptionRepo->getTransactions($subscription->subscription->id);
            $this->assertTrue(is_array($transactions));
            foreach($transactions as $transaction){
                $this->assertTrue(isset($transaction->transaction));
                $this->assertTrue(is_object($transaction->transaction));
                $this->assertTrue(isset($transaction->transaction->id));
            }
        }
    }

    public function testGetComponentsBySubscription()
    {
        $subscriptions = $this->subscriptionRepo->getSubscriptions();
        foreach ($subscriptions as $subscription) {
            $components = $this->subscriptionRepo->getComponentsBySubscription($subscription->subscription->id);
            $this->assertTrue(is_array($components));
            $this->assertTrue(!empty($components));
            foreach ($components as $component) {
                $this->assertTrue(isset($component->component));
                $this->assertTrue(is_object($component->component));
                $this->assertTrue(isset($component->component->component_id));
            }
        }
    }

//    public function testGenerateUpdatePaymentLink()
//    {
//        $subscriptions = $this->subscriptionRepo->getSubscriptions();
//        $this->assertTrue(is_array($subscriptions));
//        foreach($subscriptions as $subscription){
//            $this->assertTrue(isset($subscription->subscription));
//            $this->assertTrue(is_object($subscription->subscription));
//            $this->assertTrue(isset($subscription->subscription->id));
//            $link = $this->subscriptionRepo->generateUpdatePaymentLink($subscription->subscription->id);
//
//        }
//    }


}