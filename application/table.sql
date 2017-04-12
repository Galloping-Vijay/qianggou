insert into `tp_access`(`name`,`pid`,`row_sort`,`menu_group`,`menu`,`menu_name`,`module`,`action`) VALUES
('订单管理',0,0,1,1,'订单管理','order',''),
('商品管理',0,0,2,1,'商品管理','goods',''),
('促销管理',0,0,3,1,'促销管理','promotion',''),
('文章管理',0,0,4,1,'文章管理','article',''),
('运营管理',0,0,5,1,'运营管理','operation',''),
('系统功能',0,0,6,1,'系统功能','system',''),
('权限管理',0,0,7,1,'权限管理','access',''),
/*节点名称 父节点 排序 菜单组  ('0'不显示 1一级 2二级) 菜单名  控制器名 方法名 */
insert into `tp_access`(`name`,`pid`,`row_sort`,`menu_group`,`menu`,`menu_name`,`module`,`action`) VALUES
('订单列表',1,0,1,2,'订单列表','Order','index'),
('订单导出',1,0,1,2,'订单导出','Order','export_order'),
('订单发货',1,0,1,2,'订单发货','Order','send_order'),

('添加商品',2,0,2,2,'添加商品','Goods','good_Info'),
('商品列表',2,0,2,2,'商品列表','Goods','good_list'),
('商品属性',2,0,2,2,'商品属性','Goods','goods_attribute_list'),
('关联商品',2,0,2,2,'关联商品','Comment','goods_relate'),
('商品分类',2,0,2,2,'商品分类','Goods','category_list'),
('商品评价',2,0,2,2,'商品评价','Comment','index'),

('抢购管理',3,0,3,2,'抢购管理','Promotion','flash_sale'),
('团购管理',3,0,3,2,'团购管理','Promotion','group_buy_list'),
('商品促销',3,0,3,2,'商品促销','Promotion','prom_goods_list'),
('订单促销',3,0,3,2,'订单促销','Promotion','prom_order_list'),
('代金券管理',3,0,3,2,'代金券管理','Coupon','index'),

('文章列表',4,0,4,2,'文章列表','Article','article_List'),
('文章分类',4,0,4,2,'文章分类','Article','category_list'),

('渠道管理',5,0,5,2,'渠道管理','Operation','channel'),
('渠道统计',5,0,5,2,'渠道统计','Operation','channel_statistics'),
('订单统计',5,0,5,2,'订单统计','Operation','order_count'),
('商品统计',5,0,5,2,'商品统计','Goods','goods_count'),
('专题管理',5,0,5,2,'专题管理','Operation','project_op'),

('网站设置',6,0,6,2,'网站设置','System','index'),
('友情链接',6,0,6,2,'友情链接','Article','link_list'),
('推荐位',6,0,6,2,'推荐位','System','recommended_area'),
('普通文章',6,0,6,2,'普通文章','Article','index'),
('短信账号',6,0,6,2,'短信账号','SmsTemplate','index'),
('发送记录',6,0,6,2,'发送记录','SmsTemplate','send_log'),


('权限列表',7,0,7,2,'权限列表','System','right_list'),
('管理员列表',7,0,7,2,'管理员列表','Admin','admin_list'),
('角色管理',7,0,7,2,'角色管理','Admin','role'),
('用户组',7,0,7,2,'用户组','Member','index'),
('管理员日志',7,0,7,2,'管理员日志','Admin','log'),