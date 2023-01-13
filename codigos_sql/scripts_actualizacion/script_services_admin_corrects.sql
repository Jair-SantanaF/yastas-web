create table services_purchase_preview
(
    id int auto_increment,
    user_id int null,
    service_id int null,
    business_id int null,
    constraint services_purchase_preview_pk
        primary key (id)
);

create unique index services_purchase_preview_id_uindex
    on services_purchase_preview (id);

alter table feedback
    add fecha datetime default current_timestamp null;
