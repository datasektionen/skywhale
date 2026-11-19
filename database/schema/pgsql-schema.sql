--
-- PostgreSQL database dump
--

\restrict xFYtQW12BigxKgw6ncTEpqJdfEpDaSYH2QG7DRwtu0VOPKJ1DAjd3CErJ2vryHs

-- Dumped from database version 16.15
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: blacklist; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.blacklist (
    id integer NOT NULL,
    kth_username character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: blacklist_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.blacklist_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: blacklist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.blacklist_id_seq OWNED BY public.blacklist.id;


--
-- Name: election_position; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.election_position (
    id integer NOT NULL,
    election_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" character varying(255) NOT NULL,
    nomination_stop timestamp(0) without time zone,
    acceptance_stop timestamp(0) without time zone,
    count integer DEFAULT 1 NOT NULL
);


--
-- Name: election_position_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.election_position_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: election_position_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.election_position_id_seq OWNED BY public.election_position.id;


--
-- Name: elections; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.elections (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text NOT NULL,
    opens timestamp(0) without time zone NOT NULL,
    closes timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    acceptance_stop timestamp(0) without time zone NOT NULL,
    nomination_stop timestamp(0) without time zone NOT NULL,
    deleted_at timestamp(0) without time zone
);


--
-- Name: elections_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.elections_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: elections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.elections_id_seq OWNED BY public.elections.id;


--
-- Name: events; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.events (
    user_id integer,
    election_id integer,
    description character varying(255),
    action character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" character varying(255),
    id integer NOT NULL,
    CONSTRAINT events_action_check CHECK (((action)::text = ANY ((ARRAY['nominated'::character varying, 'accepted'::character varying, 'declined'::character varying, 'regretted'::character varying, 'opened'::character varying, 'closed'::character varying, 'nomination_stopped'::character varying, 'acceptance_stopped'::character varying])::text[])))
);


--
-- Name: events_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.events_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.events_id_seq OWNED BY public.events.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: position_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.position_user (
    id integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    election_id integer NOT NULL,
    status character varying(255) NOT NULL,
    uuid uuid NOT NULL,
    deleted_at timestamp(0) without time zone,
    "position" character varying(255) NOT NULL,
    notified timestamp(0) without time zone,
    CONSTRAINT person_position_status_check CHECK (((status)::text = ANY ((ARRAY['accepted'::character varying, 'declined'::character varying, 'waiting'::character varying])::text[])))
);


--
-- Name: person_position_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.person_position_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: person_position_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.person_position_id_seq OWNED BY public.position_user.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id integer NOT NULL,
    kth_username character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    year character varying(255) NOT NULL,
    remember_token character varying(100),
    deleted_at timestamp(0) without time zone,
    reminded timestamp(0) without time zone,
    wants_email character varying(255) DEFAULT 'unknown'::character varying NOT NULL,
    email character varying(255) NOT NULL,
    CONSTRAINT users_wants_email_check CHECK (((wants_email)::text = ANY ((ARRAY['yes'::character varying, 'no'::character varying, 'unknown'::character varying])::text[])))
);


--
-- Name: persons_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.persons_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: persons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.persons_id_seq OWNED BY public.users.id;


--
-- Name: blacklist id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blacklist ALTER COLUMN id SET DEFAULT nextval('public.blacklist_id_seq'::regclass);


--
-- Name: election_position id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.election_position ALTER COLUMN id SET DEFAULT nextval('public.election_position_id_seq'::regclass);


--
-- Name: elections id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.elections ALTER COLUMN id SET DEFAULT nextval('public.elections_id_seq'::regclass);


--
-- Name: events id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.events ALTER COLUMN id SET DEFAULT nextval('public.events_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: position_user id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.position_user ALTER COLUMN id SET DEFAULT nextval('public.person_position_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.persons_id_seq'::regclass);


--
-- Name: blacklist blacklist_kth_username_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blacklist
    ADD CONSTRAINT blacklist_kth_username_unique UNIQUE (kth_username);


--
-- Name: blacklist blacklist_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blacklist
    ADD CONSTRAINT blacklist_pkey PRIMARY KEY (id);


--
-- Name: election_position election_position_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.election_position
    ADD CONSTRAINT election_position_pkey PRIMARY KEY (id);


--
-- Name: elections elections_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.elections
    ADD CONSTRAINT elections_pkey PRIMARY KEY (id);


--
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: position_user person_position_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.position_user
    ADD CONSTRAINT person_position_pkey PRIMARY KEY (id);


--
-- Name: users persons_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT persons_pkey PRIMARY KEY (id);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX password_resets_email_index ON public.password_resets USING btree (email);


--
-- Name: password_resets_token_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX password_resets_token_index ON public.password_resets USING btree (token);


--
-- PostgreSQL database dump complete
--

\unrestrict xFYtQW12BigxKgw6ncTEpqJdfEpDaSYH2QG7DRwtu0VOPKJ1DAjd3CErJ2vryHs

--
-- PostgreSQL database dump
--

\restrict 8srgt8K42hCxObEBBYegy1hdsifcMFwnET1hcEavXGHa3PoQyWijenxSgQhqEN2

-- Dumped from database version 16.15
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_resets_table	1
3	2016_10_13_182752_create_elections_table	1
4	2016_10_13_183511_create_positions_table	1
5	2016_10_13_183906_create_election_positions_table	1
6	2016_10_13_184817_create_persons_table	1
7	2016_10_13_185022_create_person_position_table	1
8	2016_10_13_185254_add_column_election_id_to_person_position_table	1
9	2016_10_13_211420_add_column_status_to_person_position_table	1
10	2016_10_14_075510_edit_users_table	1
11	2016_10_14_075927_rename_person_to_user	1
12	2016_10_14_080138_rename_user_position_to_position_user	1
13	2016_10_14_080348_rename_person_to_user_position_user	1
14	2016_10_14_092336_add_acceptance_stop	1
15	2016_10_14_112644_add_year_column_to_users	1
16	2016_10_16_113206_add_token_column_user_position	1
17	2016_10_16_113803_add_token_column_user_position_uuid	1
18	2016_10_16_123020_add_nomination_stop_column	1
19	2016_10_16_205023_add_remember_token_to_users_table	1
20	2016_10_17_090346_add_soft_deletes	1
21	2016_10_17_170254_update_user_position_table	1
22	2016_10_17_174650_update_election_position_table	1
23	2016_10_29_140906_add_post_specific_dates	1
24	2016_10_30_181325_add_date_reminded_column	1
25	2016_10_30_192113_create_feed_table	1
26	2016_10_30_193029_change_position_type	1
27	2016_10_30_193417_add_id_event	1
28	2016_11_04_120204_create_blacklist_table	1
29	2016_11_04_133028_add_column_wants_email_to_users_table	1
30	2016_11_04_141542_add_column_notified	1
31	2023_05_05_133700_create_position_count	1
32	2025_09_24_162410_remove_ugkthid	1
33	2026_01_27_152249_add_column_email	1
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 33, true);


--
-- PostgreSQL database dump complete
--

\unrestrict 8srgt8K42hCxObEBBYegy1hdsifcMFwnET1hcEavXGHa3PoQyWijenxSgQhqEN2

