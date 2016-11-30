select * from `categories`
where `categories`.`user_id` = ?
and `categories`.`user_id` is not null
and
(
`category_name` LIKE ?
or exists
(select * from `products`
where `products`.`category_id` = `categories`.`category_id`
and (`product_name` LIKE ? or exists (select * from `sites` where `sites`.`product_id` = `products`.`product_id` and `site_url` LIKE ?)))) order by `category_order` asc, `category_id` asc limit 10 offset 0



select * from `categories` where `categories`.`user_id` = ? and `categories`.`user_id` is not null order by `category_order` asc, `category_id` asc limit 10 offset 0