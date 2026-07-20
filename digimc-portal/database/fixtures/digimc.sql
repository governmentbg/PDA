DROP SCHEMA public CASCADE ;

CREATE SCHEMA public AUTHORIZATION postgres;

COMMENT ON SCHEMA public IS 'standard public schema';

-- DROP SEQUENCE public.cultural_object_id_seq;

CREATE SEQUENCE public.cultural_object_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.cultural_object_id_seq OWNER TO postgres;
GRANT ALL ON SEQUENCE public.cultural_object_id_seq TO postgres;

-- DROP SEQUENCE public.cultural_object_id_seq1;

CREATE SEQUENCE public.cultural_object_id_seq1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.cultural_object_id_seq1 OWNER TO postgres;
GRANT ALL ON SEQUENCE public.cultural_object_id_seq1 TO postgres;

-- DROP SEQUENCE public.periodical_publication_id_seq;

CREATE SEQUENCE public.periodical_publication_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.periodical_publication_id_seq OWNER TO postgres;
GRANT ALL ON SEQUENCE public.periodical_publication_id_seq TO postgres;

-- DROP SEQUENCE public.provider_id_seq;

CREATE SEQUENCE public.provider_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.provider_id_seq OWNER TO postgres;
GRANT ALL ON SEQUENCE public.provider_id_seq TO postgres;

-- DROP SEQUENCE public.three_d_id_seq;

CREATE SEQUENCE public.three_d_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.three_d_id_seq OWNER TO postgres;
GRANT ALL ON SEQUENCE public.three_d_id_seq TO postgres;

-- DROP SEQUENCE public.three_d_id_seq1;

CREATE SEQUENCE public.three_d_id_seq1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.three_d_id_seq1 OWNER TO postgres;
GRANT ALL ON SEQUENCE public.three_d_id_seq1 TO postgres;

-- DROP SEQUENCE public.video_id_seq;

CREATE SEQUENCE public.video_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.video_id_seq OWNER TO postgres;
GRANT ALL ON SEQUENCE public.video_id_seq TO postgres;

-- DROP SEQUENCE public.video_id_seq1;

CREATE SEQUENCE public.video_id_seq1
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.video_id_seq1 OWNER TO postgres;
GRANT ALL ON SEQUENCE public.video_id_seq1 TO postgres;

-- DROP SEQUENCE public.web_resource_id_seq;

CREATE SEQUENCE public.web_resource_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	NO CYCLE;

-- Permissions

ALTER SEQUENCE public.web_resource_id_seq OWNER TO postgres;
GRANT ALL ON SEQUENCE public.web_resource_id_seq TO postgres;
-- public.provider definition

-- Drop table

-- DROP TABLE public.provider;

CREATE TABLE public.provider ( id bigserial NOT NULL, identifier varchar NOT NULL, "type" varchar NOT NULL, description varchar NULL, phone_number varchar NULL, address varchar NULL, email varchar NULL, website varchar NULL, territory varchar NULL, contact_person varchar NULL, title varchar NULL, CONSTRAINT provider_id_pk PRIMARY KEY (id));

INSERT INTO public.provider (identifier,"type",description,phone_number,address,email,website,territory,contact_person,title) VALUES
                                                                                                                      ('emf:ebe71068-3309-4285-ac7e-076808a996cd','Доставчик на данни','Доставчик на данни','+359 62 627 901','Велико Търново, ул. Ив. Ботева 2','zaemna@libraryvt.com','https://libraryvt.com/','България','Иван Иванов','Регионална библиотека „Петко Р. Славейков“ Велико Търново	'),
                                                                                                                      ('emf:497ec29b-0868-4371-b0b6-6f447df1be6e','Доставчик на данни','Доставчик на данни','+359 58 602 544','град Добрич','office@libdobrich.com','https://libdobrich.bg/','България','Иван Иванов','Регионална библиотека „Дора Габе“ Добрич	'),
                                                                                                                      ('emf:8b8bc3c7-dc5a-4311-b939-ecd24f4a77db','Доставчик на данни','Доставчик на данни',NULL,'ул. „Оборище" № 49-51, гр.Бургас',NULL,'https://mal-burgas.com/bg/biblioteka','България','Иван Иванов','Регионална библиотека „П. К. Яворов“ Бургас	'),
                                                                                                                      ('emf:50e9776b-780c-41fe-ba3c-a03ca3280f09','Доставчик на данни','Доставчик на данни','(092) 62 61 70','3000, Враца, ул. „Петропавловска“ 43','libvratsa@libvratsa.org','https://libvratsa.org/','България','Иван Иванов','Регионална библиотека „Христо Ботев“ Враца'),
                                                                                                                      ('emf:961363b5-6bb5-4c38-b417-6139b45ff3b1','Доставчик на данни','Доставчик на данни',NULL,NULL,NULL,'https://www.libsofia.bg/','България','Иван Иванов','Столична библиотека'),
                                                                                                                      ('emf:788e6782-ba06-4ef4-9e59-740bb8913829','Доставчик на данни','Доставчик на данни','(+ 359 82) 820 130','Русе, 7000, ул. "Дондуков-Корсаков" № 1','libruse@libruse.bg','https://www.libruse.bg/','България','Иван Иванов','Регионална библиотека „Любен Каравелов“ Русе'),
                                                                                                                      ('emf:c9c26e22-407b-400e-ac48-83c16f8d6039','Доставчик на данни','Доставчик на данни','(+ 359 32) 654 901, 912','4000 Пловдив, ул. "Авксентий Велешки'''' № 17','nbiv@libplovdiv.com','https://libplovdiv.com/index.php/bg/','България','Иван Иванов','Народна библиотека „Иван Вазов“ Пловдив'),
                                                                                                                      ('emf:f35cea44-7397-434e-915f-00582e0c2f99','Доставчик на данни','Доставчик на данни','+ 359 (2) 9183 212','София 1504, бул. “Васил Левски” 88','nl@nationallibrary.bg','https://www.nationallibrary.bg/','България','Иван Иванов','Национална библиотека „Св. св. Кирил и Методий“ София'),
                                                                                                                      ('emf:1a7828f5-34f1-4fad-b901-8e15cc7b0022','Доставчик на данни','Доставчик на данни','+359 56 82 03 44','ул. Славянска 69, 8000 гр. Бургас','main@burgasmuseums.bg','https://www.burgasmuseums.bg/','България','Иван Иванов','Регионален исторически музей Бургас'),
                                                                                                                      ('emf:e0e0fed6-fd74-4eb7-bacc-31f89bf540c8','Доставчик на данни','Доставчик на данни','092/624457','пл. „Христо Ботев” №2, 3000 гр. Враца','vratsamuseum@mail.bg','https://www.vratsamuseum.com/','България','Иван Иванов','Регионален исторически музей Враца');
INSERT INTO public.provider (identifier,"type",description,phone_number,address,email,website,territory,contact_person,title) VALUES
                                                                                                                      ('emf:13184536-3918-4aab-a052-5dce1683850d','Доставчик на данни','Доставчик на данни','034/44 31 08','гр. Пазарджик, пл. Константин Величков № 15','museumpz@abv.bg','https://museum-pz.com/wp/','България','Иван Иванов','Регионален исторически музей Пазарджик'),
                                                                                                                      ('emf:9158dc6d-e65a-4e6d-bda9-64801b4f75c9','Доставчик на данни','Доставчик на данни','073 / 88 53 70','ул. Рила 1, кв. Вароша, Благоевград','info@blagomuseum.org, rimbld@gmail.com','https://blagomuseum.org/','България','Иван Иванов','Регионален исторически музей Благоевград'),
                                                                                                                      ('emf:769da4fd-bb98-4b0d-8114-39b57ab80ac5','Доставчик на данни','Доставчик на данни','+359 (885) 105-282','ул. „Иван Вазов“, № 38, Велико Търново 5000','rimvt@abv.bg','https://www.museumvt.com/','България','Иван Иванов','Регионален исторически музей Велико Търново'),
                                                                                                                      ('emf:4bac3643-f726-433d-946a-056df5238a29','Доставчик на данни','Доставчик на данни','094/ 601707','3700 Видин, ул. “Цар Симеон Велики” 13','museumvd@mail.bg','https://rimvidin.bg/','България','Иван Иванов','Регионален исторически музей Видин'),
                                                                                                                      ('emf:5347ca24-3e45-4d7b-af8e-265c79278abe','Доставчик на данни','Доставчик на данни','+35996307481','град Монтана, пощенски код: 3400, ул. “Цар Борис III“ №2','bgmontanamuseum@abv.bg, rim@montanahm.eu','https://montanahm.eu/','България','Иван Иванов','Регионален исторически музей Монтана'),
                                                                                                                      ('emf:4b3a1198-619a-422c-b3b2-935ee5b689f6','Доставчик на данни','Доставчик на данни','+359 32 633096','ул. Христо Г. Данов 34, Пловдив 4000, България','pnm_plovdiv@abv.bg','https://rnhm.org/','България','Иван Иванов','Регионален природонаучен музей Пловдив'),
                                                                                                                      ('emf:afe943a3-2ef6-48bf-ad08-20482d6edf08','Доставчик на данни','Доставчик на данни','+359 2 988 49 22','пл. „Св. Александър Невски“, ул. „19 февруари“ № 1, 1000 София','office@nationalgallery.bg','https://nationalgallery.bg/','България','Иван Иванов','Национална галерия София'),
                                                                                                                      ('emf:1d25cea6-8a14-4761-af7a-12ec3bfd8a81','Доставчик на данни','Доставчик на данни','+359 42/ 919 207','6000 Стара Загора, бул. Руски №42','rim@rimstz.eu','https://www.rimstz.eu/','България','Иван Иванов','Регионален исторически музей Стара Загора'),
                                                                                                                      ('emf:5123de13-bdc5-4db1-bebb-feca30c0d287','Доставчик на данни','Доставчик на данни','076 60 37 37','2300 Перник ул. Физкултурна №2','museum_pernik@abv.bg','https://museumpernik.com/','България','Иван Иванов','Регионален исторически музей Перник'),
                                                                                                                      ('emf:f60dce83-b12d-4011-8c8e-62deec9d9983','Доставчик на данни','Доставчик на данни','+359 82 825 002','7000 РУСЕ, пл. „Aл.Батенберг” 3, ПК 60','pr@museumruse.com','https://www.museumruse.com/','България','Иван Иванов','Регионален исторически музей Русе');
INSERT INTO public.provider (identifier,"type",description,phone_number,address,email,website,territory,contact_person,title) VALUES
                                                                                                                      ('emf:80bf0910-a247-4147-9f15-8524653429aa','Доставчик на данни','Доставчик на данни','088 429-80-05','ул. „Ген. Дерожински“ №144, Габрово 5309','museum@etar.bg','https://etar.bg/','България','Иван Иванов','Регионален етнографски музей на открито „Етър“ Габрово'),
                                                                                                                      ('emf:6680a935-d3d0-4821-b6e7-66ae91d28543','Доставчик на данни','Доставчик на данни','0301/6 27 27','гр. Смолян 4700, ул.”Дичо Петров” №5','rim.smolyan@gmail.com','https://museumsmolyan.eu/','България','Иван Иванов','Регионален исторически музей Смолян'),
                                                                                                                      ('emf:4d46540e-2391-44bc-8700-7cb234586472','Доставчик на данни','Доставчик на данни','+35958602642','гр. Добрич, ул. „Д-р Константин Стоилов“ 18','rim_dobrich@abv.bg','https://www.dobrichmuseum.bg/','България','Иван Иванов','Регионален исторически музей Добрич'),
                                                                                                                      ('emf:23218692-cd6d-4219-99de-c367aaf36cce','Доставчик на данни','Доставчик на данни','+359 892 28 04 44','гр. Шумен, бул. “Славянски” № 17','museum_shumen@abv.bg','https://museum-shumen.eu/','България','Иван Иванов','Регионален исторически музей Шумен'),
                                                                                                                      ('emf:857a8f87-92d1-442e-b373-e751fdb7f430','Доставчик на данни','Доставчик на данни','+359 2 865 03 18','София, площад Бански 1','biblioteka@sofiahistorymuseum.bg','https://www.sofiahistorymuseum.bg/bg/','България','Иван Иванов','Регионален исторически музей София'),
                                                                                                                      ('emf:db7ba253-a25e-4247-ab2a-727f28987af2','Доставчик на данни','Доставчик на данни','044 622 494','град Сливен, бул. "Цар Освободител" №18','museum_pr@abv.bg','https://museum.sliven.net/','България','Иван Иванов','Регионален исторически музей Сливен'),
                                                                                                                      ('emf:f8dc28b6-9206-469d-88ea-870200b6a70d','Доставчик на данни','Доставчик на данни',NULL,NULL,NULL,'https://haskovomuseum.com/','България','Иван Иванов','Регионален исторически музей Хасково'),
                                                                                                                      ('emf:b7cc6117-a305-402e-be33-6dc4699a8286','Доставчик на данни','Доставчик на данни','+359 601 65216','бул. “Митрополит Андрей” №2, гр. Търговище 7700','trgmuseum@abv.bg','https://museumtg.com/','България','Иван Иванов','Регионален исторически музей Търговище'),
                                                                                                                      ('emf:66516cc0-dc38-4c95-a50a-802b27ce29d0','Доставчик на данни','Доставчик на данни','0895541369','Кюстендил, бул." България " 55 п.к. 253','rmuseum.kn@abv.bg','http://www.kyustendilmuseum.primasoft.bg/bg/index.php','България','Иван Иванов','Регионален исторически музей Кюстендил'),
                                                                                                                      ('emf:4bbd2f91-398b-461f-adfb-3717b21fd381','Доставчик на данни','Доставчик на данни','+ 359 52 681 011','Варна, бул. “Мария Луиза” 41','rimvarna@abv.bg','https://www.museumvarna.com/','България','Иван Иванов','Регионален исторически музей Варна');
INSERT INTO public.provider (identifier,"type",description,phone_number,address,email,website,territory,contact_person,title) VALUES
                                                                                                                      ('emf:74eeb991-2e45-4f44-953f-687910b67b9d','Доставчик на данни','Доставчик на данни','+35932629409','Пловдив, 4000, Площад „Съединение“ № 1','info@historymuseumplovdiv.org','https://rimplovdiv.wordpress.com/','България','Иван Иванов','Регионален исторически музей Пловдив'),
                                                                                                                      ('emf:9ade6b33-38c4-4634-b706-5b5ca6ca7745','Доставчик на данни','Доставчик на данни','0878 901439','гр. Разград, бул. "Априлско въстание" 70',NULL,'https://abritus.bg/','България','Иван Иванов','Регионален исторически музей Разград'),
                                                                                                                      ('emf:ba108662-9c25-40ba-8fdc-fac729d0ad21','Доставчик на данни','Доставчик на данни','086/820 388','ул. „Г. С. Раковски” №24, Силистра 7500','museumsilistra@abv.bg','https://museumsilistra.com/bg/','България','Иван Иванов','Регионален исторически музей Силистра'),
                                                                                                                      ('emf:b7c44d2e-605f-4088-9c8b-5c61c9d1fcf6','Доставчик на данни','Доставчик на данни','+ 359 877 04 31 54','Ямбол 8600 ул. „Бяло море“ 2','museum.yambol@gmail.com','https://yambolmuseum.eu/','България','Иван Иванов','Регионален исторически музей Ямбол'),
                                                                                                                      ('emf:6cf388c0-8df7-40fb-aca6-e47a9ec03541','Доставчик на данни','Доставчик на данни','+359-(0)2-955 76 04','ул. „Витошко лале“ 16, София 1618','nim.pr@historymuseum.org','https://historymuseum.org/','България','Иван Иванов','Национален исторически музей София'),
                                                                                                                      ('emf:6bff6d80-f7b5-49d6-8dbc-a35fce324d80','Доставчик на данни','Доставчик на данни','(02) 979 6611','ул. Акад. Г. Бончев, блок 2, 1113 - София','iictbas.bg','https://iict.bas.bg/','България','Иван Иванов','Институт по информационни и комуникационни технологии БАН');

-- Permissions

ALTER TABLE public.provider OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.provider TO postgres;


-- public.web_resource definition

-- Drop table

-- DROP TABLE public.web_resource;

CREATE TABLE public.web_resource ( id bigserial NOT NULL, identifier varchar NOT NULL, "type" varchar NOT NULL, creator varchar NULL, description varchar NULL, format varchar NULL, rights_holder varchar NULL, resource_type varchar NULL, conforms_to varchar NULL, created_at varchar NULL, extent varchar NULL, issued varchar NULL, web_resource_address varchar NULL, rights varchar NULL, sensitive_content varchar NULL, content_warning varchar NULL, warning_text varchar NULL, CONSTRAINT web_resource_pk PRIMARY KEY (id));

ALTER TABLE web_resource
    ADD COLUMN visualizationtype TEXT;

-- Column comments

COMMENT ON COLUMN public.web_resource.visualizationtype IS 'Tип на визуализацията - дали като image, tiff, pdf, audio, video, 3d или misc (ако не е някой от тези 5)';
COMMENT ON COLUMN public.web_resource.creator IS 'Автор/създател на ресурса';
COMMENT ON COLUMN public.web_resource.description IS 'Описание	Performance with Buccin trombone';
COMMENT ON COLUMN public.web_resource.conforms_to IS 'Посочва се стандарт, на който ресурсът отговаря. Например, W3C WCAG 2.0 (стандарт за достъпност на уеб съдържание).';
COMMENT ON COLUMN public.web_resource.created_at IS 'Дата на създаване на ресурса. Въвежда се в свободен текст.';
COMMENT ON COLUMN public.web_resource.extent IS 'Допълнителна информация за размерите на ресурса и/или за продължителност на записа (за аудио или видео файлове).';
COMMENT ON COLUMN public.web_resource.issued IS 'Дата на издаване/публикуване на ресурса. Въвежда се в свободен текст.';
COMMENT ON COLUMN public.web_resource.web_resource_address IS 'Уеб адрес на ресурса, от който може да бъде достъпен свободно.';
COMMENT ON COLUMN public.web_resource.rights IS 'Определя се с какви права е ресурсът - свободно ползване, ограничени права, т.н. Стойностите са от Creative Commons, каквито се ползват и в Европеана.';
COMMENT ON COLUMN public.web_resource.sensitive_content IS 'Маркира се дали ресурсът показва чувствително съдържание. При наличие на чувствително съдържание, то се визуализира след като потребителят първо е информиран.';
COMMENT ON COLUMN public.web_resource.content_warning IS 'Отбелязва се от какъв характер е чувствителното съдържание.';
COMMENT ON COLUMN public.web_resource.warning_text IS 'Въвежда се текст, който описва по-детайлно от какъв характер е чувствителното съдържание. Този текст се визуализира на крайния потребител в портала.';

-- Permissions

ALTER TABLE public.web_resource OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.web_resource TO postgres;


-- public.code_value definition

-- Drop table

-- DROP TABLE public.code_value;

CREATE TABLE public.code_value ( id bigserial NOT NULL, code varchar NOT NULL, value_bg varchar NULL, value_en varchar NULL, CONSTRAINT code_value_unique UNIQUE (code));

-- Permissions

ALTER TABLE public.code_value OWNER TO postgres;
GRANT ALL ON TABLE public.code_value TO postgres;


-- public.cultural_object definition

-- Drop table

-- DROP TABLE public.cultural_object;

CREATE TABLE public.cultural_object ( identifier varchar NOT NULL, "type" varchar NOT NULL, title varchar NOT NULL, original_title varchar NULL, other_title varchar NULL, artist varchar NULL, description varchar NULL, cultural_object_provided_by int8 NULL, creation_date varchar NULL, current_location varchar NULL, keywords varchar NULL, theme varchar NULL, subject_heading varchar NULL, geographic_heading varchar NULL, temporal_heading varchar NULL, language_code varchar NULL, physical_dimensions varchar NULL, medium varchar NULL, previous_owner varchar NULL, acquisition varchar NULL, original_media varchar NULL, rights_holder varchar NULL, contentdescription varchar NULL, id bigserial NOT NULL, amount numeric NULL, currency varchar NULL, CONSTRAINT cultural_object_pk PRIMARY KEY (id), CONSTRAINT cultural_object_provider_fk FOREIGN KEY (cultural_object_provided_by) REFERENCES public.provider(id));


ALTER TABLE cultural_object
    ADD COLUMN thumbnail_url TEXT,
ADD COLUMN extended_view_url TEXT;

-- Column comments

COMMENT ON COLUMN public.cultural_object.thumbnail_url IS 'Тъмбнейл за показване в списъци.';
COMMENT ON COLUMN public.cultural_object.extended_view_url IS 'Допълнителен thumbnail.';
COMMENT ON COLUMN public.cultural_object.identifier IS 'Уникален идентификатор на обекта в системата. Създава се автоматично.';
COMMENT ON COLUMN public.cultural_object."type" IS 'Тип';
COMMENT ON COLUMN public.cultural_object.title IS 'Наименование на културния обект - заглавие на книга, заглавие на филм, наименование на паметник, наименование на артефакт, и др.';
COMMENT ON COLUMN public.cultural_object.original_title IS 'Оригинално заглавие на културната ценност, ако има такова. Чуждоезичните текстови артефакти може да имат оригинално заглавие.';
COMMENT ON COLUMN public.cultural_object.other_title IS 'Други заглавия';
COMMENT ON COLUMN public.cultural_object.artist IS 'Име или имена на хора, които са създали културната ценност.';
COMMENT ON COLUMN public.cultural_object.description IS 'Детайлно описание на обекта.';
COMMENT ON COLUMN public.cultural_object.cultural_object_provided_by IS 'Музей, библиотека или друга институция, която доставя и поддържа данните за културната ценност.';
COMMENT ON COLUMN public.cultural_object.creation_date IS 'Дата или период на създаване на обекта.';
COMMENT ON COLUMN public.cultural_object.current_location IS 'Текущо местоположение на културната ценност.';
COMMENT ON COLUMN public.cultural_object.keywords IS 'Ключови думи, даващи информация за културната ценност.';
COMMENT ON COLUMN public.cultural_object.theme IS 'Дава информация за тематиката на обекта. Може да съдържа много стойности.';
COMMENT ON COLUMN public.cultural_object.subject_heading IS 'Ключови думи или фрази, които обозначават темата на културната ценност.';
COMMENT ON COLUMN public.cultural_object.geographic_heading IS 'Уточнение за мястото, за което се отнася културната ценност.';
COMMENT ON COLUMN public.cultural_object.temporal_heading IS 'Уточнение за времето, за което се отнася културната ценност.';
COMMENT ON COLUMN public.cultural_object.language_code IS 'Език/езици, на който е написан обекта.';
COMMENT ON COLUMN public.cultural_object.physical_dimensions IS 'Описание на размерите на обекта - 39 x 29, 5; 50 x 35 см';
COMMENT ON COLUMN public.cultural_object.medium IS 'Описание на материала, от който е създаден обекта, както и техника, използвана за създаването му.';
COMMENT ON COLUMN public.cultural_object.previous_owner IS 'Предишен собственик на обекта, ако има такъв.';
COMMENT ON COLUMN public.cultural_object.acquisition IS 'Информация относно начина на придобиване на културната ценност от музея/бибиотеката. Може да се опише и накратко местонамирането на обекта.';
COMMENT ON COLUMN public.cultural_object.original_media IS 'Оригинален носител на културната ценност - филм, хартиен носител, дигитален файл';
COMMENT ON COLUMN public.cultural_object.contentdescription IS 'Описание на съдържанието. Съдържа творения на Дионисий Ареопагит и схолите на Максим изповедник върху Дионисиевото съчинение';

-- Permissions

ALTER TABLE public.cultural_object OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.cultural_object TO postgres;


-- public.has_component definition

-- Drop table

-- DROP TABLE public.has_component;

CREATE TABLE public.has_component ( cultural_object_id int8 NOT NULL, cultural_object_child_id int8 NOT NULL, CONSTRAINT has_component_cultural_object_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id), CONSTRAINT has_component_cultural_object_fk_1 FOREIGN KEY (cultural_object_child_id) REFERENCES public.cultural_object(id));

-- Permissions

ALTER TABLE public.has_component OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.has_component TO postgres;


-- public.has_web_view definition

-- Drop table

-- DROP TABLE public.has_web_view;

CREATE TABLE public.has_web_view ( cultural_object_id int8 NOT NULL, web_resource_id int8 NOT NULL, CONSTRAINT has_web_view_web_resource_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id), CONSTRAINT has_web_view_web_resource_fk_1 FOREIGN KEY (web_resource_id) REFERENCES public.web_resource(id));

-- Permissions

ALTER TABLE public.has_web_view OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.has_web_view TO postgres;


-- public.has_web_view_additional definition

-- Drop table

-- DROP TABLE public.has_web_view_additional;

CREATE TABLE public.has_web_view_additional ( cultural_object_id int8 NOT NULL, web_resource_id int8 NOT NULL, CONSTRAINT has_web_view_additional_web_resource_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id), CONSTRAINT has_web_view_additional_web_resource_fk_1 FOREIGN KEY (web_resource_id) REFERENCES public.web_resource(id));

-- Permissions

ALTER TABLE public.has_web_view_additional OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.has_web_view_additional TO postgres;


-- public.has_web_view_extended definition

-- Drop table

-- DROP TABLE public.has_web_view_extended;

CREATE TABLE public.has_web_view_extended ( cultural_object_id int8 NOT NULL, web_resource_id int8 NOT NULL, CONSTRAINT has_web_view_extended_web_resource_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id), CONSTRAINT has_web_view_extended_web_resource_fk_1 FOREIGN KEY (web_resource_id) REFERENCES public.web_resource(id));

-- Permissions

ALTER TABLE public.has_web_view_extended OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.has_web_view_extended TO postgres;


-- public.has_web_view_thumbnail definition

-- Drop table

-- DROP TABLE public.has_web_view_thumbnail;

CREATE TABLE public.has_web_view_thumbnail ( cultural_object_id int8 NOT NULL, web_resource_id int8 NOT NULL, CONSTRAINT has_web_view_thumbnail_web_resource_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id), CONSTRAINT has_web_view_thumbnail_web_resource_fk_1 FOREIGN KEY (web_resource_id) REFERENCES public.web_resource(id));

-- Permissions

ALTER TABLE public.has_web_view_thumbnail OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.has_web_view_thumbnail TO postgres;


-- public.image definition

-- Drop table

-- DROP TABLE public.image;

CREATE TABLE public.image ( id int8 NOT NULL, creation_date varchar NULL, publisher varchar NULL, issuer_person varchar NULL, issuing_year int4 NULL, cultural_object_id int8 NOT NULL, sub_type varchar NOT NULL, CONSTRAINT image_pkey PRIMARY KEY (id), CONSTRAINT image_cultural_object_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id));

-- Column comments

COMMENT ON COLUMN public.image.creation_date IS 'Дата на създаване на изображението.';
COMMENT ON COLUMN public.image.publisher IS 'Име на издателството за текстова културна ценност.';
COMMENT ON COLUMN public.image.issuer_person IS 'Име на лице, издател на текстовата културна ценност. За Сиджил в това поле се вписва стойността за "Име на кадията"';
COMMENT ON COLUMN public.image.issuing_year IS 'Година на отпечатване или създаване на изображението (свободен текст).';

-- Permissions

ALTER TABLE public.image OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.image TO postgres;


-- public.part_of definition

-- Drop table

-- DROP TABLE public.part_of;

CREATE TABLE public.part_of ( cultural_object_child_id int8 NOT NULL, cultural_object_parent_id int8 NOT NULL, CONSTRAINT part_of_cultural_object_fk FOREIGN KEY (cultural_object_child_id) REFERENCES public.cultural_object(id), CONSTRAINT part_of_cultural_object_fk_1 FOREIGN KEY (cultural_object_parent_id) REFERENCES public.cultural_object(id));

-- Permissions

ALTER TABLE public.part_of OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.part_of TO postgres;


-- public.text_object definition

-- Drop table

-- DROP TABLE public.text_object;

CREATE TABLE public.text_object ( id int8 NOT NULL, year_of_publication int4 NULL, date_of_publication varchar NULL, translator varchar NULL, writer varchar NULL, sponsor varchar NULL, issuer_person varchar NULL, publisher varchar NULL, first_аuthor varchar NULL, cultural_object_id int8 NOT NULL, sub_type varchar NOT NULL, issuing_institution varchar NULL, CONSTRAINT text_object_id_pk PRIMARY KEY (id), CONSTRAINT text_object_cultural_object_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id));

-- Column comments

COMMENT ON COLUMN public.text_object.year_of_publication IS 'Година/период на публикуване на обекта (в свободен текст).';
COMMENT ON COLUMN public.text_object.date_of_publication IS 'Дата на публикуване на обекта (в свободен текст).';
COMMENT ON COLUMN public.text_object.translator IS 'Име на преводача, ако има такъв.';
COMMENT ON COLUMN public.text_object.writer IS 'Име на писателя.';
COMMENT ON COLUMN public.text_object.sponsor IS 'Име на други хора, които имат принос за написването на изданието.';
COMMENT ON COLUMN public.text_object.issuer_person IS 'Име на лице, издател на текстовата културна ценност. За Сиджил в това поле се вписва стойността за "Име на кадията"';
COMMENT ON COLUMN public.text_object.publisher IS 'Име на издателството за текстова културна ценност.';
COMMENT ON COLUMN public.text_object.first_аuthor IS 'Име на първи автор, ако има такъв.';
COMMENT ON COLUMN public.text_object.issuing_institution IS 'Институция-издател на документа.';

-- Permissions

ALTER TABLE public.text_object OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.text_object TO postgres;


-- public.three_d definition

-- Drop table

-- DROP TABLE public.three_d;

CREATE TABLE public.three_d ( id bigserial NOT NULL, publisher varchar NULL, cultural_object_id int8 NOT NULL, sub_type varchar NOT NULL, design_house varchar NULL, CONSTRAINT three_d_id_pkey PRIMARY KEY (id), CONSTRAINT three_d_cultural_object_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id));

-- Column comments

COMMENT ON COLUMN public.three_d.publisher IS 'Издател';

-- Permissions

ALTER TABLE public.three_d OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.three_d TO postgres;


-- public.video definition

-- Drop table

-- DROP TABLE public.video;

CREATE TABLE public.video ( id bigserial NOT NULL, years_issued int4 NULL, film_duration varchar NULL, actor varchar NULL, filmmaker varchar NULL, scenario_writer varchar NULL, cameraman varchar NULL, producer varchar NULL, composer varchar NULL, mount varchar NULL, production_director varchar NULL, editor varchar NULL, other_related_persons varchar NULL, premiere varchar NULL, cultural_object_id int8 NOT NULL, sub_type varchar NOT NULL, interviewer varchar NULL, interviewee varchar NULL, CONSTRAINT video_id_pk PRIMARY KEY (id), CONSTRAINT video_cultural_object_fk FOREIGN KEY (cultural_object_id) REFERENCES public.cultural_object(id));

-- Column comments

COMMENT ON COLUMN public.video.years_issued IS 'Година на първо излъчване';
COMMENT ON COLUMN public.video.film_duration IS 'Продължителност на видеото';
COMMENT ON COLUMN public.video.actor IS 'Имена на актьорите, участници във видеото';
COMMENT ON COLUMN public.video.filmmaker IS 'Имена на режисьорите на видеото';
COMMENT ON COLUMN public.video.scenario_writer IS 'Имена на сценаристите на видеото';
COMMENT ON COLUMN public.video.cameraman IS 'Имена на операторите на видеото';
COMMENT ON COLUMN public.video.producer IS 'Имена на продуцентите на видеото';
COMMENT ON COLUMN public.video.composer IS 'Имена на композиторите на музиката към видеото';
COMMENT ON COLUMN public.video.mount IS 'Имена на хората, които са изпълнили монтажа на видеото';
COMMENT ON COLUMN public.video.production_director IS 'Имена на директорите на продукцията';
COMMENT ON COLUMN public.video.editor IS 'Имена на редакторите на видеото';
COMMENT ON COLUMN public.video.other_related_persons IS 'Други лица, свързани с видеото';
COMMENT ON COLUMN public.video.premiere IS 'Премиера';
COMMENT ON COLUMN public.video.interviewee IS 'Имена на интервюирания/те';

-- Permissions

ALTER TABLE public.video OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.video TO postgres;


-- public.audio definition

-- Drop table

-- DROP TABLE public.audio;

CREATE TABLE public.audio ( id int8 NOT NULL, performer varchar NULL, producer varchar NULL, duration varchar NULL, recording_team varchar NULL, audio_original_title varchar NULL, composer varchar NULL, author_of_arrangement varchar NULL, text_author varchar NULL, editing_producer_name varchar NULL, date_recorded varchar NULL, broadcasting_date varchar NULL, colutral_object_id int8 NOT NULL, sub_type varchar NOT NULL, interviewer varchar NULL, interviewee varchar NULL, CONSTRAINT audio_id_pk PRIMARY KEY (id), CONSTRAINT audio_cultural_object_fk FOREIGN KEY (colutral_object_id) REFERENCES public.cultural_object(id));

-- Column comments

COMMENT ON COLUMN public.audio.performer IS 'Изпълнител на записа';
COMMENT ON COLUMN public.audio.producer IS 'Продуцент или звукозаписна компания';
COMMENT ON COLUMN public.audio.duration IS 'Продължителност на записа';
COMMENT ON COLUMN public.audio.recording_team IS 'Имена на инженери, записали материала';
COMMENT ON COLUMN public.audio.audio_original_title IS 'Оригинално заглавие на музикалния албум, ако има такова';
COMMENT ON COLUMN public.audio.composer IS 'Имена на композитор/и';
COMMENT ON COLUMN public.audio.author_of_arrangement IS 'Имена на автора на аранжимента';
COMMENT ON COLUMN public.audio.text_author IS 'Имена на автора на текста';
COMMENT ON COLUMN public.audio.editing_producer_name IS 'Имена на продуцента на записа';
COMMENT ON COLUMN public.audio.date_recorded IS 'Дата, на която е направен записът';
COMMENT ON COLUMN public.audio.broadcasting_date IS 'Дата на първо излъчване на записа';

-- Permissions

ALTER TABLE public.audio OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.audio TO postgres;


-- public.periodical_publication definition


-- Inserts
insert
into
    cultural_object (
    identifier,
    type,
    title,
    original_title,
    other_title,
    artist,
    description,
    cultural_object_provided_by,
    creation_date,
    current_location,
    keywords,
    theme,
    subject_heading ,
    geographic_heading,
    temporal_heading,
    language_code,
    physical_dimensions,
    medium,
    previous_owner,
    acquisition,
    original_media,
    rights_holder,
    contentdescription,
    amount,
    currency,
    thumbnail_url,
    extended_view_url

)
values (
           'emf:dc915525-ff9b-447b-b4f8-72b94c16c776',
           'text',
           'Нов ориенталски документ',
           'Нов ориенталски документ',
           '',
           'Захари Жандов',
           'Някакво описание',
           30,
           '2025-08-21',
           'Варна',
           '',
           'Тема',
           'Нов ориенталски документ',
           '',
           '',
           'BG',
           '',
           '',
           'Предишен собственик - Гюнеш Шефкедов',
           'Придобиване - стойност',
           'Оригинална медиа - Text',
           'Right holder - Гюнеш',
           '',
           200000,
           'EUR',
           '',
           ''
       );


insert
into
    text_object (
    id,
    year_of_publication ,
    date_of_publication ,
    translator,
    writer,
    sponsor,
    issuer_person,
    publisher,
    first_аuthor,
    cultural_object_id,
    sub_type,
    issuing_institution
)
values(
          1,
          '1984',
          '1984-08-21',
          'John Translator',
          'John Writer',
          'Jojn Sponsor',
          'John Issuer',
          'John Publisher',
          'John the first author',
          1,
          'MCD454002',
          'Institution'
      );


insert into web_resource (
    identifier,
    type, -- тип на уеб ресурса - дали е основна или допълнителна. Взема се от кодлист таблиците
    creator,
    description,
    format,
    rights_holder,
    resource_type,
    conforms_to,
    created_at,
    extent,
    issued,
    web_resource_address, -- публичен URL с достъп до уеб ресурса
    rights,
    sensitive_content ,
    content_warning ,
    warning_text,
    visualizationtype -- тип на визуализацията - дали като image, tiff, pdf, audio, video, 3d или misc (ако не е някой от тези 5)
)values(
           'emf:3c539ba6-8a1b-4ab6-b359-8ec986e79c75',
           'MCD454001', -- тип на уеб ресурса - дали е основна или допълнителна. Взема се от кодлист таблиците
           'Creator',
           'Some description',
           '00:08:33',
           'Rights holder',
           'application/pdf',
           'John Doe',
           '2025-08-21',
           '00:08:33',
           '',
           'https://cdn3.trilio-tech.org/docs/80/de/de5d66b3-17d1-4907-90b1-3b5751ff0ff4/original/pachebel-canon-in-d.pdf',
           'MCD399001',
           'MCD448001',
           '',
           '',
           'pdf'
       );


insert
into
    cultural_object (
    identifier,
    type,
    title,
    original_title,
    other_title,
    artist,
    description,
    cultural_object_provided_by,
    creation_date,
    current_location,
    keywords,
    theme,
    subject_heading ,
    geographic_heading,
    temporal_heading,
    language_code,
    physical_dimensions,
    medium,
    previous_owner,
    acquisition,
    original_media,
    rights_holder,
    contentdescription,
    amount,
    currency,
    thumbnail_url,
    extended_view_url

)
values (
           'emf:f484e6e2-6dad-40e5-8b4d-f3b897eba2ac',
           'movie',
           'Старо време',
           'Старо време',
           '',
           'Захари Жандов',
           'Някакво описание',
           30,
           '2025-08-21',
           'Варна',
           '',
           'Тема',
           'Старо време',
           '',
           '',
           'BG',
           '',
           '',
           'Предишен собственик - Гюнеш Шефкедов',
           'Придобиване - стойност',
           'Оригинална медиа - AVI',
           'Right holder - Гюнеш',
           '',
           100000,
           'EUR',
           'https://cdn3.trilio-tech.org/video/video/13/13b070cc-8779-4205-8a52-e5398cc26e95/preview/thumb_240_01.jpg',
           'https://cdn3.trilio-tech.org/video/video/13/13b070cc-8779-4205-8a52-e5398cc26e95/preview/thumb_240_02.jpg'
       );


insert
into
    video (years_issued,
           film_duration,
           actor,
           filmmaker,
           scenario_writer,
           cameraman,
           producer,
           composer,
           mount,
           production_director,
           editor,
           other_related_persons,
           premiere,
           cultural_object_id,
           sub_type,
           interviewer,
           interviewee)
values (
           1942,
           '01:30:54',
           'Актьор: Стоян Бъчваров',
           '',
           'Димитър Минков',
           'Димитър Минков',
           '„Балкан филм“ – Димитър Минков',
           'Николай Терзиев',
           'Димитър Минков',
           'Димитър Минков',
           'Димитър Минков',
           '',
           '12 ноември 1945 – кино „Македония“',
           2,
           'Филм',
           '',''

       );


insert into web_resource (
    identifier,
    type,  -- тип на уеб ресурса - дали е основна или допълнителна. Взема се от кодлист таблиците
    creator,
    description,
    format,
    rights_holder,
    resource_type,
    conforms_to,
    created_at,
    extent,
    issued,
    web_resource_address,   --публичен URL с достъп до уеб ресурса
    rights,
    sensitive_content ,
    content_warning ,
    warning_text,
    visualizationtype --тип на визуализацията - дали като image, tiff, pdf, audio, video, 3d или misc (ако не е някой от тези 5)
)values(
           'emf:0394fa3c-3bb9-47e8-8bb1-62e4ff211d9b',
           'MCD454001',  --- тип на уеб ресурса - дали е основна или допълнителна. Взема се от кодлист таблиците
           'Creator',
           'Some description',
           '00:08:33',
           'Rights holder',
           'audio/mpegURL',
           'John Doe',
           '2025-08-21',
           '00:08:33',
           '',
           'https://cdn3.trilio-tech.org/video/video/13/13b070cc-8779-4205-8a52-e5398cc26e95/hls/master.m3u8',
           'MCD399001',
           'MCD448001',
           '',
           '',
           'video'
       );


insert into cultural_object (
    identifier,
    type,
    title,
    original_title,
    other_title,
    artist,
    description,
    cultural_object_provided_by,
    creation_date,
    current_location,
    keywords,
    theme,
    subject_heading,
    geographic_heading,
    temporal_heading,
    language_code,
    physical_dimensions,
    medium,
    previous_owner,
    acquisition,
    original_media,
    rights_holder,
    contentdescription,
    amount,
    currency,
    thumbnail_url,
    extended_view_url
)
values (
           'emf:9cc7c231-f572-4229-8488-888665f93a85',
           'image',
           'Rock-and-girl',
           'Rock-and-girl',
           '',
           'Захари Жандов',
           'Някакво описание',
           30,
           '2025-08-21',
           'Варна',
           '',
           'Тема',
           'Rock-and-girl',
           '',
           '',
           'BG',
           '',
           '',
           'Предишен собственик - Гюнеш Шефкедов',
           'Придобиване - стойност',
           'Оригинална медиа - PNG',
           'Right holder - Гюнеш',
           '',
           100000,
           'EUR',
           'https://cdn3.trilio-tech.org/video/video/13/13b070cc-8779-4205-8a52-e5398cc26e95/preview/thumb_240_01.jpg',
           'https://cdn3.trilio-tech.org/video/video/13/13b070cc-8779-4205-8a52-e5398cc26e95/preview/thumb_240_02.jpg'
       );

insert into image (
    id,
    creation_date,
    publisher,
    issuer_person,
    issuing_year,
    cultural_object_id,
    sub_type
)
values (
           1,
           '1994-08-21',
           'John Publisher',
           'John Issuer',
           1994,
           3,
           'MCD454002'
       );

insert into web_resource (
    identifier,
    type,  -- тип на уеб ресурса
    creator,
    description,
    format,
    rights_holder,
    resource_type,
    conforms_to,
    created_at,
    extent,
    issued,
    web_resource_address,   -- публичен URL
    rights,
    sensitive_content,
    content_warning,
    warning_text,
    visualizationtype
)
values (
           'emf:9cc7c231-f572-4229-8488-888665f93a86',
           'MCD454001',
           'Creator',
           'Some description',
           '00:08:33',
           'Rights holder',
           'image/png',
           'John Doe',
           '2025-08-21',
           '00:08:33',
           '',
           'https://cdn3.trilio-tech.org/image/80/6f/6f889579-92dc-46bd-84ba-6475b4d73e56/original/rock-and-girl.png',
           'MCD399001',
           'MCD448001',
           '',
           '',
           'image'
       );

insert
into
    cultural_object (
    identifier,
    type,
    title,
    original_title,
    other_title,
    artist,
    description,
    cultural_object_provided_by,
    creation_date,
    current_location,
    keywords,
    theme,
    subject_heading ,
    geographic_heading,
    temporal_heading,
    language_code,
    physical_dimensions,
    medium,
    previous_owner,
    acquisition,
    original_media,
    rights_holder,
    contentdescription,
    amount,
    currency,
    thumbnail_url,
    extended_view_url

)
values (
           'emf:210027ec-a4ca-4be1-8b40-3280bf3f2a77',
           'movie',
           'Бамби',
           'Бамби',
           '',
           'Bambi Жандов',
           'Бамби описание',
           30,
           '2025-08-21',
           'Варна',
           '',
           'Тема',
           'Бамби',
           '',
           '',
           'BG',
           '',
           '',
           'Предишен собственик - Бамби Шефкедов',
           'Придобиване - стойност',
           'MCD406001',
           'Right holder - Бамби',
           '',
           100000,
           'EUR',
           'https://cdn3.trilio-tech.org/image/188/08/08c1f73b-b90e-40bd-9ede-679b7b9c5e36/original/kinopregled.jpg',
           'https://cdn3.trilio-tech.org/image/188/63/6378ed10-9a7c-4a20-9c7b-03357abd191b/original/MC-DIGI-(6).jpg'
       );


insert
into video (
    years_issued,
    film_duration,
    actor,
    filmmaker,
    scenario_writer,
    cameraman,
    producer,
    composer,
    mount,
    production_director,
    editor,
    other_related_persons,
    premiere,
    cultural_object_id,
    sub_type,
    interviewer,
    interviewee)
values (
           1942,
           '01:30:54',
           'Актьор: Бамби Бъчваров',
           '',
           'Бамби Минков',
           'Бамби Минков',
           '„Балкан филм“ – Бамби Минков',
           'Николай Терзиев',
           'Бамби Минков',
           'Бамби Минков',
           'Бамби Минков',
           '',
           '12 ноември 1945 – кино „Македония“',
           4,
           'Филм',
           '',''
       );

-- Основна визуализация - видео файл
insert into web_resource (
    identifier,
    type,
    creator,
    description,
    format,
    rights_holder,
    resource_type,
    conforms_to,
    created_at,
    extent,
    issued,
    web_resource_address,
    rights,
    sensitive_content ,
    content_warning ,
    warning_text,
    visualizationtype
)values(
           'emf:0394fa3c-3bb9-47e8-8bb1-62e4ff211d9b',
           'MCD454001',
           'Creator',
           'Some description',
           '00:08:33',
           'Rights holder',
           'audio/mpegURL',
           'John Doe',
           '2025-08-21',
           '00:08:33',
           '',
           'https://cdn3.trilio-tech.org/video/video/06/062fc095-00c9-4a7f-baee-f616afed4b2a/hls/master.m3u8',
           'MCD399001',
           'MCD448001',
           '',
           '',
           'video'
       );


insert into web_resource (
    identifier,
    type,
    creator,
    description,
    format,
    rights_holder,
    resource_type,
    conforms_to,
    created_at,
    extent,
    issued,
    web_resource_address,
    rights,
    sensitive_content ,
    content_warning ,
    warning_text,
    visualizationtype
)values(
           'emf:4a24db46-2a7a-4ea6-8a44-8749251ef23a',
           'MCD454002',
           'Creator',
           'Some description - images',
           '00:08:33',
           'Rights holder - image',
           'image/jpeg',
           'John Doe',
           '2025-08-21',
           '00:08:33',
           '',
           'https://cdn3.trilio-tech.org/image/188/63/6378ed10-9a7c-4a20-9c7b-03357abd191b/original/MC-DIGI-(6).jpg',
           'MCD399001',
           'MCD448001',
           'MCD450001',
           '',
           'image'
       );


insert into web_resource (
    identifier,
    type,
    creator,
    description,
    format,
    rights_holder,
    resource_type,
    conforms_to,
    created_at,
    extent,
    issued,
    web_resource_address,
    rights,
    sensitive_content ,
    content_warning ,
    warning_text,
    visualizationtype
)values(
           'emf:8b1f04e3-3225-4eb4-bf78-4ed0e6c02358',
           'MCD454002',
           'Creator',
           'Some description - pdf',
           '00:08:33',
           'Rights holder - pdf',
           'application/pdf',
           'John Doe',
           '2025-08-21',
           '00:08:33',
           '',
           'https://cdn3.trilio-tech.org/docs/188/cb/cb5e36f3-415f-4ffd-b416-121593d2f2a3/original/UI-developer''s-guide.pdf',
           'MCD399001',
           'MCD448001',
           'MCD450001',
           '',
           'pdf'
       );

INSERT INTO has_web_view (cultural_object_id, web_resource_id) VALUES
                                                                   (1, 1),
                                                                   (2, 2),
                                                                   (3, 3);

insert into has_web_view (cultural_object_id, web_resource_id) values (4,4);
insert into has_web_view (cultural_object_id, web_resource_id) values (4,5);
insert into has_web_view (cultural_object_id, web_resource_id) values (4,6);
-- Drop table

-- DROP TABLE public.periodical_publication;

CREATE TABLE public.periodical_publication ( id bigserial NOT NULL, periodical_publication_type varchar NULL, text_object_id int8 NOT NULL, CONSTRAINT periodical_publication_id_pk PRIMARY KEY (id), CONSTRAINT periodical_publication_text_object_fk FOREIGN KEY (text_object_id) REFERENCES public.text_object(id));

-- Column comments

COMMENT ON COLUMN public.periodical_publication.periodical_publication_type IS 'Тип на печатното издание.	Бюлетин, Вестник, Списание';

-- Permissions

ALTER TABLE public.periodical_publication OWNER TO postgres;
GRANT TRUNCATE, TRIGGER, SELECT, INSERT, UPDATE, REFERENCES, DELETE ON TABLE public.periodical_publication TO postgres;


-- public.periodical_publication_number definition

-- Drop table

-- DROP TABLE public.periodical_publication_number;

CREATE TABLE public.periodical_publication_number ( id bigserial NOT NULL, periodical_publication_issue_number varchar NULL, periodical_publication_issue_year varchar NULL, periodical_publication_issue_month varchar NULL, periodical_publication_issue_day varchar NULL, text_object_id int8 NULL, periodical_publication_id int8 NULL, CONSTRAINT periodical_publication_number_pk PRIMARY KEY (id), CONSTRAINT periodical_publication_number_periodical_publication_fk FOREIGN KEY (periodical_publication_id) REFERENCES public.periodical_publication(id), CONSTRAINT periodical_publication_number_text_object_fk FOREIGN KEY (text_object_id) REFERENCES public.text_object(id));

-- Permissions

ALTER TABLE public.periodical_publication_number OWNER TO postgres;
GRANT ALL ON TABLE public.periodical_publication_number TO postgres;




-- Permissions

GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO public;
