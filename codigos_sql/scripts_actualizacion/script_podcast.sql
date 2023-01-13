CREATE TABLE `podcast` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `title` varchar(200) DEFAULT NULL,
                           `description` text DEFAULT NULL,
                           `preview` varchar(50) DEFAULT NULL,
                           `type` varchar(5) DEFAULT NULL,
                           `audio` varchar(200) DEFAULT NULL,
                           `date` date DEFAULT NULL,
                           `duration` time DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `podcast_id_uindex` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

create table podcast_comments
(
    id int auto_increment,
    comment text null,
    podcast_id int null,
    user_id int null,
    likes int(5) null,
    datetime datetime default current_timestamp null,
    constraint podcast_comments_pk
        primary key (id)
);

create unique index podcast_comments_id_uindex
    on podcast_comments (id);

create table podcast_like
(
    id int auto_increment,
    podcast_id int null,
    user_id int null,
    constraint podcast_like_pk
        primary key (id)
);

create unique index podcast_like_id_uindex
    on podcast_like (id);


alter table podcast_comments alter column likes set default 0;

alter table podcast_like change podcast_id comment_id int null;
