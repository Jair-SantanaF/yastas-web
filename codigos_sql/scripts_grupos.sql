
create table `groups`
(
    id int auto_increment,
    name varchar(200) null,
    business_id int(11) null,
    constraint groups_pk
        primary key (id)
);

create unique index groups_id_uindex
    on `groups` (id);

create table elearning_groups
(
    id int(11) auto_increment,
    group_id int(11) null,
    elearning_id int(11) null,
    constraint elearning_groups_pk
        primary key (id)
);

create unique index elearning_groups_id_uindex
    on elearning_groups (id);

create table users_groups
(
    id int(11) auto_increment,
    user_id int(11) null,
    group_id int(11) null,
    constraint users_groups_pk
        primary key (id)
);

create unique index users_groups_id_uindex
    on users_groups (id);

CREATE TABLE `library_groups` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `group_id` int(11) DEFAULT NULL,
                                  `library_id` int(11) DEFAULT NULL,
                                  PRIMARY KEY (`id`),
                                  UNIQUE KEY `library_groups_id_uindex` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

alter table `groups`
    add active tinyint(1) default 1 null;

alter table users_groups
    add active tinyint(1) default 1 null;

alter table library_groups
    add active tinyint(1) default 1 null;

alter table elearning_modules
    add active tinyint(1) default 1 null;

alter table elearning_groups
    add active tinyint(1) default 1 null;

alter table numbers_employees
    add group_id int null;

alter table numbers_employees
    add active tinyint(1) default 1 null;

alter table numbers_employees
    add business_id int null;

alter table user
    add register_no_invitation tinyint(1) default 0 null;

alter table user alter column profile_photo set default 'https://kreativeco.com/basf/assets/img/default_user.png';