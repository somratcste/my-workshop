<?php

//uses 01

$pending_line_items = app(OrderService::class)->getOrderLineItemsWithPendingQtyByOrder($id);

// Sql from service
public function getOrderLineItemsWithPendingQtyByOrder($orderId)
{

    return \DB::select("
              Select *, case when qty - fulfiled_qty - confirmed_qty = 0 then 0 else qty - fulfiled_qty - confirmed_qty end pending_qty from
                (select
                orders.id as order_id,
                order_line_items.id as order_line_item_id,
                order_line_items.product_id,
                order_line_items.qty,
              (select sku from stock_records where stock_records.id = order_line_items.stock_record_id) as sku,
              (select count(*)
                  from stock_items
                  where stock_items.order_id = orders.id
                  and stock_items.stock_record_id = order_line_items.stock_record_id
                  and (stock_items.status = ? or stock_items.status = ? or stock_items.status = ?)) as fulfiled_qty,
                (select COALESCE (sum(confirmed_qty), 0)
                  from order_line_items_virtual_stocks
                  where order_line_items_virtual_stocks.order_line_item_id = order_line_items.id
                  and order_line_items_virtual_stocks.purchase_order_id is null) as confirmed_qty
                from orders
                join order_line_items on orders.id = order_line_items.order_id
                where orders.id = ?) vt where qty - fulfiled_qty - confirmed_qty > 0", [StockItemStatus::ASSIGNED, StockItemStatus::PACKED, StockItemStatus::SOLD, $orderId]);
}

// uses 02 from request

$order_data = app(OrderService::class)->getPendingQtyByOrderLineItems($request['order_line_item_id']);

// Sql from service

public function getPendingQtyByOrderLineItems($orderLineItemId)
{
    return \DB::select("
           select 
           order_line_items.id,
           order_line_items.qty,
           order_line_items.order_id,
           order_line_items.qty,
           (select count(*) 
             from stock_items 
             where stock_items.order_id = order_line_items.order_id 
             and stock_items.stock_record_id = order_line_items.stock_record_id 
             and stock_items.status = ?) as fulfiled_qty,
           (select COALESCE (sum(confirmed_qty), 0)
             from order_line_items_virtual_stocks 
             where 
               order_line_items_virtual_stocks.order_line_item_id = order_line_items.id) as confirmed_qty 
           FROM order_line_items 
             where order_line_items.id = ? limit 1",
        [StockItemStatus::ASSIGNED, $orderLineItemId])[0];
}

// uddate by model events sql

public function resetConfirmedQty( $orderLineItem )
{
    DB::select("update virtual_stocks vs set
                    vs.confirmed_qty = vs.confirmed_qty - ?
                    where vs.id = ?", [$orderLineItem->confirmed_qty, $orderLineItem->virtual_stock_id]);
}