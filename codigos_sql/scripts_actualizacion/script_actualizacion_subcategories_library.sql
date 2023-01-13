CREATE TABLE `library_subcategory` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
                                       `subcategory` varchar(255) DEFAULT NULL,
                                       `active` int(1) DEFAULT NULL,
                                       `business_id` int(11) DEFAULT NULL,
                                       `category_id` int(11) DEFAULT NULL,
                                       PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

alter table library_elements_ drop column area_id;
