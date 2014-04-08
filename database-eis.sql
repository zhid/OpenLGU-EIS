--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: development_fund_trigger_function(); Type: FUNCTION; Schema: public; Owner: openlgu
--

CREATE FUNCTION development_fund_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ 

						BEGIN 

						RAISE NOTICE 'Trigger Created!'; 

 RETURN NEW 

;

						END 

;

						$$;


ALTER FUNCTION public.development_fund_trigger_function() OWNER TO mypguser;

--
-- Name: enrollment_rate_in_secondary_schools_trigger_function(); Type: FUNCTION; Schema: public; Owner: openlgu
--

CREATE FUNCTION enrollment_rate_in_secondary_schools_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ 

						BEGIN 

						RAISE NOTICE 'Trigger Created!'; 

 RETURN NEW 

;

						END 

;

						$$;


ALTER FUNCTION public.enrollment_rate_in_secondary_schools_trigger_function() OWNER TO mypguser;

--
-- Name: household_sewage_disposal_trigger_function(); Type: FUNCTION; Schema: public; Owner: openlgu
--

CREATE FUNCTION household_sewage_disposal_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ 

						BEGIN 

						RAISE NOTICE 'Trigger Created!'; 

 RETURN NEW 

;

						END 

;

						$$;


ALTER FUNCTION public.household_sewage_disposal_trigger_function() OWNER TO mypguser;

--
-- Name: rat_infestation_damage_trigger_function(); Type: FUNCTION; Schema: public; Owner: openlgu
--

CREATE FUNCTION rat_infestation_damage_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ 

												BEGIN 

  IF (NEW.estimated_loss > 150000

											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES (2, 5, 'high threat level', concat_ws(' ', 'Estimated Loss', 'is', NEW.estimated_loss,  'for', NEW.barangay_surveyed,  'for', NEW.crop_commodities_affected,  'which is within the threshold of ', 'high threat level'), now()); 

 END IF; 

 IF (NEW.estimated_loss >= 150000

											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES (2, 5, 'low threat level', concat_ws(' ', 'Estimated Loss', 'is', NEW.estimated_loss,  'for', NEW.barangay_surveyed,  'for', NEW.crop_commodities_affected,  'which is within the threshold of ', 'low threat level'), now()); 

 END IF; 

 RETURN NEW 

;

												END 

;

												$$;


ALTER FUNCTION public.rat_infestation_damage_trigger_function() OWNER TO mypguser;

--
-- Name: solid_waste_disposal_trigger_function(); Type: FUNCTION; Schema: public; Owner: openlgu
--

CREATE FUNCTION solid_waste_disposal_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ 

						BEGIN 

						RAISE NOTICE 'Trigger Created!'; 

 RETURN NEW 

;

						END 

;

						$$;


ALTER FUNCTION public.solid_waste_disposal_trigger_function() OWNER TO mypguser;

--
-- Name: statement_of_expenditures_trigger_function(); Type: FUNCTION; Schema: public; Owner: openlgu
--

CREATE FUNCTION statement_of_expenditures_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ 

												BEGIN 

  IF (NEW.fund_used > 500000000

											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES (25, 31, 'high threat level', concat_ws(' ', 'Fund Used', 'is', NEW.fund_used,  'for', NEW.services,  'for', NEW.specific_allotment,  'which is within the threshold of ', 'high threat level'), now()); 

 END IF; 

 RETURN NEW 

;

												END 

;

												$$;


ALTER FUNCTION public.statement_of_expenditures_trigger_function() OWNER TO mypguser;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: alert; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE alert (
    alert_id integer NOT NULL,
    measure_id integer NOT NULL,
    column_id integer NOT NULL,
    alert_type character varying(30) NOT NULL,
    description text NOT NULL,
    date timestamp without time zone NOT NULL,
    read boolean DEFAULT false NOT NULL
);


ALTER TABLE public.alert OWNER TO mypguser;

--
-- Name: alert_alert_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE alert_alert_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.alert_alert_id_seq OWNER TO mypguser;

--
-- Name: alert_alert_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE alert_alert_id_seq OWNED BY alert.alert_id;


--
-- Name: area_id_sequence; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE area_id_sequence
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.area_id_sequence OWNER TO mypguser;

--
-- Name: area; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE area (
    area_id integer DEFAULT nextval('area_id_sequence'::regclass) NOT NULL,
    area_name character varying(100) NOT NULL,
    color_rating integer DEFAULT 1 NOT NULL,
    managing_office character varying(100),
    officer_in_charge character varying(100),
    visible boolean DEFAULT true NOT NULL,
    service_area smallint,
    area_logo character varying(30)
);


ALTER TABLE public.area OWNER TO mypguser;

--
-- Name: column_dimension; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE column_dimension (
    measure_id integer,
    column_id integer NOT NULL,
    column_name character varying(100),
    column_data_type character varying(30)
);


ALTER TABLE public.column_dimension OWNER TO mypguser;

--
-- Name: column_column_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE column_column_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.column_column_id_seq OWNER TO mypguser;

--
-- Name: column_column_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE column_column_id_seq OWNED BY column_dimension.column_id;


--
-- Name: column_hierarchy; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE column_hierarchy (
    column_hierarchy_id integer NOT NULL,
    category_id integer NOT NULL,
    parent_id integer DEFAULT 0 NOT NULL,
    top_flag boolean DEFAULT true NOT NULL,
    bottom_flag boolean DEFAULT true NOT NULL,
    distance_level integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.column_hierarchy OWNER TO mypguser;

--
-- Name: column_hierarchy_column_hierarchy_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE column_hierarchy_column_hierarchy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.column_hierarchy_column_hierarchy_id_seq OWNER TO mypguser;

--
-- Name: column_hierarchy_column_hierarchy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE column_hierarchy_column_hierarchy_id_seq OWNED BY column_hierarchy.column_hierarchy_id;


--
-- Name: development_fund; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE development_fund (
    program text,
    specific_appropriation text,
    fund_released bigint
);


ALTER TABLE public.development_fund OWNER TO mypguser;

--
-- Name: enrollment_rate_in_secondary_schools; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE enrollment_rate_in_secondary_schools (
    school_location text,
    school_type text,
    school_name text,
    number_of_male_students bigint,
    number_of_female_students bigint,
    total_number_of_students bigint
);


ALTER TABLE public.enrollment_rate_in_secondary_schools OWNER TO mypguser;

--
-- Name: household_sewage_disposal; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE household_sewage_disposal (
    type_of_disposal text,
    number_of_households bigint
);


ALTER TABLE public.household_sewage_disposal OWNER TO mypguser;

--
-- Name: threshold; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE threshold (
    threshold_id integer NOT NULL,
    measure_id integer NOT NULL,
    column_id integer NOT NULL,
    lowthreshold double precision DEFAULT 0,
    lowthreshold_operator character varying(5) DEFAULT '='::character varying,
    threshold_type character varying DEFAULT 'low threat level'::character varying,
    highthreshold double precision DEFAULT 0,
    highthreshold_operator character varying(5) DEFAULT '='::character varying
);


ALTER TABLE public.threshold OWNER TO mypguser;

--
-- Name: indicator_indicator_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE indicator_indicator_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.indicator_indicator_id_seq OWNER TO mypguser;

--
-- Name: indicator_indicator_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE indicator_indicator_id_seq OWNED BY threshold.threshold_id;


--
-- Name: measure; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE measure (
    measure_id integer NOT NULL,
    measure_name character varying(100),
    alert_level integer DEFAULT 0 NOT NULL,
    area_id integer NOT NULL,
    description text
);


ALTER TABLE public.measure OWNER TO mypguser;

--
-- Name: measure_measure_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE measure_measure_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.measure_measure_id_seq OWNER TO mypguser;

--
-- Name: measure_measure_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE measure_measure_id_seq OWNED BY measure.measure_id;


--
-- Name: rat_infestation_damage; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE rat_infestation_damage (
    barangay_surveyed text,
    crop_commodities_affected text,
    farmers_affected double precision,
    total_area_planted double precision,
    estimated_area_damage double precision,
    estimated_loss double precision,
    cost_of_damage double precision
);


ALTER TABLE public.rat_infestation_damage OWNER TO mypguser;

--
-- Name: row_dimension; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE row_dimension (
    measure_id integer,
    row_id integer NOT NULL,
    row_name character varying(100),
    row_data_type character varying(30) NOT NULL
);


ALTER TABLE public.row_dimension OWNER TO mypguser;

--
-- Name: row_hierarchy; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE row_hierarchy (
    row_hierarchy_id integer NOT NULL,
    category_id integer NOT NULL,
    parent_id integer DEFAULT 0 NOT NULL,
    top_flag boolean DEFAULT true NOT NULL,
    bottom_flag boolean DEFAULT true NOT NULL,
    distance_level integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.row_hierarchy OWNER TO mypguser;

--
-- Name: row_hierarchy_row_hierarchy_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE row_hierarchy_row_hierarchy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.row_hierarchy_row_hierarchy_id_seq OWNER TO mypguser;

--
-- Name: row_hierarchy_row_hierarchy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE row_hierarchy_row_hierarchy_id_seq OWNED BY row_hierarchy.row_hierarchy_id;


--
-- Name: row_row_id_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE row_row_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.row_row_id_seq OWNER TO mypguser;

--
-- Name: row_row_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE row_row_id_seq OWNED BY row_dimension.row_id;


--
-- Name: solid_waste_disposal; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE solid_waste_disposal (
    method_solid_waste_disposal text,
    number_of_households bigint
);


ALTER TABLE public.solid_waste_disposal OWNER TO mypguser;

--
-- Name: statement_of_expenditures; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE statement_of_expenditures (
    services text,
    specific_allotment text,
    fund_used double precision
);


ALTER TABLE public.statement_of_expenditures OWNER TO mypguser;

--
-- Name: user_identification; Type: TABLE; Schema: public; Owner: openlgu; Tablespace: 
--

CREATE TABLE user_identification (
    username character varying(50) NOT NULL,
    password character varying(10) NOT NULL,
    role character varying(30),
    area_id integer,
    userid integer NOT NULL
);


ALTER TABLE public.user_identification OWNER TO mypguser;

--
-- Name: user_identification_userid_seq; Type: SEQUENCE; Schema: public; Owner: openlgu
--

CREATE SEQUENCE user_identification_userid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_identification_userid_seq OWNER TO mypguser;

--
-- Name: user_identification_userid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: openlgu
--

ALTER SEQUENCE user_identification_userid_seq OWNED BY user_identification.userid;


--
-- Name: alert_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY alert ALTER COLUMN alert_id SET DEFAULT nextval('alert_alert_id_seq'::regclass);


--
-- Name: column_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY column_dimension ALTER COLUMN column_id SET DEFAULT nextval('column_column_id_seq'::regclass);


--
-- Name: column_hierarchy_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY column_hierarchy ALTER COLUMN column_hierarchy_id SET DEFAULT nextval('column_hierarchy_column_hierarchy_id_seq'::regclass);


--
-- Name: measure_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY measure ALTER COLUMN measure_id SET DEFAULT nextval('measure_measure_id_seq'::regclass);


--
-- Name: row_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY row_dimension ALTER COLUMN row_id SET DEFAULT nextval('row_row_id_seq'::regclass);


--
-- Name: row_hierarchy_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY row_hierarchy ALTER COLUMN row_hierarchy_id SET DEFAULT nextval('row_hierarchy_row_hierarchy_id_seq'::regclass);


--
-- Name: threshold_id; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY threshold ALTER COLUMN threshold_id SET DEFAULT nextval('indicator_indicator_id_seq'::regclass);


--
-- Name: userid; Type: DEFAULT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY user_identification ALTER COLUMN userid SET DEFAULT nextval('user_identification_userid_seq'::regclass);


--
-- Data for Name: alert; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY alert (alert_id, measure_id, column_id, alert_type, description, date, read) FROM stdin;
6	2	2	low threat level	Farmers Affected is 45 for Colosas for Cacao which is within the threshold of  low threat level	2014-02-28 00:00:00	f
7	2	2	low threat level	Farmers Affected is 10 for Colosas for Mongo which is within the threshold of  low threat level	2014-02-28 00:00:00	f
8	2	2	low threat level	Farmers Affected is 45 for Colosas for Gabi which is within the threshold of  low threat level	2014-02-28 00:00:00	f
9	2	2	low threat level	Farmers Affected is 5 for Colosas for Upland Rice which is within the threshold of  low threat level	2014-02-28 00:00:00	f
10	25	31	high threat level	Fund Used is 513771372 for General Services for Personal Services which is within the threshold of  high threat level	2014-03-07 00:00:00	f
11	25	31	high threat level	Fund Used is 1213174412 for General Services for Maintenance and Operating Expenses which is within the threshold of  high threat level	2014-03-07 00:00:00	f
12	25	31	high threat level	Fund Used is 843055801 for Economic Services for Maintenance and Operating Expenses which is within the threshold of  high threat level	2014-03-07 00:00:00	f
14	2	2	low threat level	Farmers Affected is 2 for Colosas for Soybeans which is within the threshold of  low threat level	2014-03-07 00:00:00	f
15	2	2	low threat level	Farmers Affected is 9 for Colosas for Peanut which is within the threshold of  low threat level	2014-03-07 00:00:00	f
16	2	2	low threat level	Farmers Affected is 2 for Colosas for Coffee which is within the threshold of  low threat level	2014-03-07 00:00:00	f
2	2	2	moderate threat level	Farmers Affected is 309 for Colosas for Banana which is within the threshold of  moderate threat level	2014-02-28 00:00:00	t
3	2	2	low threat level	Farmers Affected is 201 for Colosas for Cassava which is within the threshold of  low threat level	2014-02-28 00:00:00	t
13	25	31	high threat level	Fund Used is 755054436 for Social Services for Maintenance and Operating Expenses which is within the threshold of  high threat level	2014-03-07 00:00:00	t
4	2	2	low threat level	Farmers Affected is 250 for Colosas for Camote which is within the threshold of  low threat level	2014-02-28 00:00:00	t
5	2	2	low threat level	Farmers Affected is 74 for Colosas for Coconut which is within the threshold of  low threat level	2014-02-28 00:00:00	t
\.


--
-- Name: alert_alert_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('alert_alert_id_seq', 16, true);


--
-- Data for Name: area; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY area (area_id, area_name, color_rating, managing_office, officer_in_charge, visible, service_area, area_logo) FROM stdin;
20	Financial Accountability	2	Accounting Office	-	t	4	fa fa-money fa-5x
25	Proper Household Waste Disposal	1	Municipal Health Office	-	t	5	fa fa-home fa-5x
23	Project Planning	1	-	-	t	1	fa fa-tasks fa-5x
21	Tourism	1	-	-	t	2	fa fa-home fa-5x
27	Support to Education	1	-	-	t	2	fa fa-book fa-5x
2	Agriculture	3	City Department of Agriculture	None	t	3	fa fa-leaf fa-5x
\.


--
-- Name: area_id_sequence; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('area_id_sequence', 27, true);


--
-- Name: column_column_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('column_column_id_seq', 46, true);


--
-- Data for Name: column_dimension; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY column_dimension (measure_id, column_id, column_name, column_data_type) FROM stdin;
2	2	Farmers Affected	double precision
2	3	Total Area Planted	double precision
2	4	Estimated Area Damage	double precision
2	5	Estimated Loss	double precision
2	6	Cost of Damage	double precision
25	31	Fund Used	double precision
32	38	Fund Released	bigint
33	39	Number of Households	bigint
34	40	Number of Households	bigint
36	44	Number of Male Students	bigint
36	45	Number of Female Students	bigint
36	46	Total Number of Students	bigint
\.


--
-- Data for Name: column_hierarchy; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY column_hierarchy (column_hierarchy_id, category_id, parent_id, top_flag, bottom_flag, distance_level) FROM stdin;
31	31	31	t	t	0
33	37	37	t	t	0
34	38	38	t	t	0
35	39	39	t	t	0
36	40	40	t	t	0
40	44	44	t	t	0
41	45	45	t	t	0
42	46	46	t	t	0
2	2	2	t	t	0
3	3	3	t	t	0
4	4	4	t	t	0
5	5	5	t	t	0
6	6	6	t	t	0
22	22	22	t	t	0
24	24	24	t	t	0
25	25	27	f	t	1
26	26	27	f	t	1
27	27	27	t	f	0
28	28	28	t	t	0
\.


--
-- Name: column_hierarchy_column_hierarchy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('column_hierarchy_column_hierarchy_id_seq', 42, true);


--
-- Data for Name: development_fund; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY development_fund (program, specific_appropriation, fund_released) FROM stdin;
Infrastructure Development	Construction of Waiting Shed	7500000
Infrastructure Development	Construction of Pathwalk	1000000
Infrastructure Development	Beautification of River Park	9000000
Infrastructure Development	Eco Tourism Development Project	2000000
Infrastructure Development	Construction of Roads, Highway, Bridges etc	12949000
Barangay Development Program	Maintenance of Brgy MRF	8850000
Barangay Development Program	Maintenance of Roads	5900000
Barangay Development Program	Aid to Community Development	5750000
Livestock Agro-Processing Center	Purchase of Steam Boiler	950000
Livestock Agro-Processing Center	Purchase of Gambrels	180000
Livestock Agro-Processing Center	Additional Railings System	350000
Livestock and Poultry Development Program	Chicken Dispersal	500000
Amortization for Development Projects	Expenses for landfill and Dumptrucks	3395000
Amortization for Development Projects	Expenses for school construction	4799000
Amortization for Development Projects	Expenses for construction of hospitals	19800000
Amortization for Development Projects	Expenses for public markets	2940000
Amortization for Development Projects	Expenses for Agro Proc Center	7412000
\.


--
-- Data for Name: enrollment_rate_in_secondary_schools; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY enrollment_rate_in_secondary_schools (school_location, school_type, school_name, number_of_male_students, number_of_female_students, total_number_of_students) FROM stdin;
Salapawan	Private	A P Guevarra IS	124	93	217
\.


--
-- Data for Name: household_sewage_disposal; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY household_sewage_disposal (type_of_disposal, number_of_households) FROM stdin;
Sewage Pipe	43
Septic Tank	78
Underground Pit	54
Underground Communal	13
Pan Collection	30
\.


--
-- Name: indicator_indicator_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('indicator_indicator_id_seq', 17, true);


--
-- Data for Name: measure; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY measure (measure_id, measure_name, alert_level, area_id, description) FROM stdin;
2	Rat Infestation Damage	0	2	Consolidated Rat Infestation Damage Report
25	Statement of Expenditures	0	20	Statement of Expenditures
32	Development Fund	0	23	Development Fund
33	Household Sewage Disposal	0	25	Number of households using different disposal methods
34	Solid Waste Disposal	0	25	Method of Solid Waste Disposal Per Household
36	Enrollment Rate in Secondary Schools	0	27	Enrollment Rate
\.


--
-- Name: measure_measure_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('measure_measure_id_seq', 36, true);


--
-- Data for Name: rat_infestation_damage; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY rat_infestation_damage (barangay_surveyed, crop_commodities_affected, farmers_affected, total_area_planted, estimated_area_damage, estimated_loss, cost_of_damage) FROM stdin;
Salapawan	Corn	104	152.75	107	53.5	374237
Salapawan	Cassava	34	15.25	10.5	74.700000000000003	597800
Salapawan	Camote	43	11.5	8	64.400000000000006	322000
Salapawan	Banana	3	0.75	0.29999999999999999	2.3999999999999999	16800
Salapawan	Gabi	26	5.25	3.7000000000000002	18.399999999999999	128625
Salapawan	Upland Ricce	2	2	1.3999999999999999	1.3999999999999999	14000
Lumiad	Corn	112	151.75	106.2	53.100000000000001	371787
Lumiad	Cacao	68	71.75	28.699999999999999	43	3444000
Lumiad	Banana	91	76.25	30.5	244	1708000
Lumiad	Coconut	9	16.5	3.2999999999999998	9900	99000
Lumiad	Peanut	2	1.25	0.5	0.5	17000
Colosas	Corn	647	328.75	230.09999999999999	115.09999999999999	805350
Colosas	Banana	309	328.75	230.09999999999999	115.09999999999999	805350
Colosas	Cassava	201	29.75	20.800000000000001	145.80000000000001	1166200
Colosas	Camote	250	91.75	64.200000000000003	513.79999999999995	2569800
Colosas	Coconut	74	44.5	8.9000000000000004	26700	267000
Colosas	Cacao	45	15.75	6.2999999999999998	9.5	756000
Colosas	Mongo	10	2.75	1.8999999999999999	1.8999999999999999	15400
Colosas	Gabi	45	10	7	35	245000
Colosas	Upland Rice	5	2.5	1.75	1.75	17500
Colosas	Soybeans	2	0.75	0.29999999999999999	0.29999999999999999	3000
Colosas	Peanut	9	2.25	0.90000000000000002	4.5	157500
Colosas	Coffee	2	1	0.20000000000000001	0.29999999999999999	4500
Tapak	Corn	286	229	160.30000000000001	80.150000000000006	561050
Tapak	Banana	42	11	4.4000000000000004	35.200000000000003	246400
Tapak	Cassava	167	66.5	46.600000000000001	372.39999999999998	1862000
Tapak	Gabi	101	50.75	35.5	248.69999999999999	1989400
Tapak	Peanut	23	6	4.2000000000000002	21	147000
Tapak	Mongo	4	1.5	0.59999999999999998	0.59999999999999998	4800
Tapak	Upland Rice	4	2.25	1.5	1.6000000000000001	15750
Tapak	Tomato	1	0.25	0.25	0.25	2500
Mapula	Corn	197	292	204.40000000000001	102.2	715400
Mapula	Cassava	109	27.75	19.5	135.90000000000001	1087800
Mapula	Camote	126	37	25.899999999999999	207.19999999999999	1036000
Mapula	Peanut	21	9.5	3.7999999999999998	3.7999999999999998	240000
Mapula	Cacao	13	5	2	3	133000
Mapula	Banana	189	102.75	41.100000000000001	328.80000000000001	2301600
Mapula	Coconut	41	29.5	5.9000000000000004	17700	177000
Mapula	Coffee	5	4.5	0.90000000000000002	1.3500000000000001	67500
Mapula	Gabi	21	4.75	3.2999999999999998	16.600000000000001	116375
Mapula	Upland Rice	2	5	3.5	3.5	35000
Fatima	Corn	197	94.189999999999998	65.900000000000006	329.60000000000002	2307200
\.


--
-- Data for Name: row_dimension; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY row_dimension (measure_id, row_id, row_name, row_data_type) FROM stdin;
2	2	Barangay Surveyed	text
2	3	Crop Commodities Affected	text
25	32	Services	text
25	33	Specific Allotment	text
32	41	Program	text
32	42	Specific Appropriation	text
33	43	Type of Disposal	text
34	44	Method Solid Waste Disposal	text
36	48	School Location	text
36	49	School Type	text
36	50	School Name	text
\.


--
-- Data for Name: row_hierarchy; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY row_hierarchy (row_hierarchy_id, category_id, parent_id, top_flag, bottom_flag, distance_level) FROM stdin;
37	41	41	t	f	0
38	42	41	f	t	1
32	32	32	t	f	0
33	33	32	f	t	1
39	43	43	t	t	0
40	44	44	t	t	0
44	48	48	t	t	0
45	49	49	t	t	0
46	50	50	t	t	0
2	2	2	t	f	0
3	3	2	f	t	1
\.


--
-- Name: row_hierarchy_row_hierarchy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('row_hierarchy_row_hierarchy_id_seq', 46, true);


--
-- Name: row_row_id_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('row_row_id_seq', 50, true);


--
-- Data for Name: solid_waste_disposal; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY solid_waste_disposal (method_solid_waste_disposal, number_of_households) FROM stdin;
Disposal to Sanitary Landfill	93
Incinerated	67
Dumped of burned in the open	42
Recycled	27
Others	14
\.


--
-- Data for Name: statement_of_expenditures; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY statement_of_expenditures (services, specific_allotment, fund_used) FROM stdin;
General Services	Personal Services	513771372
General Services	Maintenance and Operating Expenses	1213174412
General Services	Capital Outlay	48217571
Economic Services	Personal Services	209853451
Economic Services	Maintenance and Operating Expenses	843055801
Economic Services	Capital Outlay	26718983
Social Services	Personal Services	264996072
Social Services	Maintenance and Operating Expenses	755054436
Social Services	Capital Outlay	4961150
General Service	Year-end Bonus	231496671
\.


--
-- Data for Name: threshold; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY threshold (threshold_id, measure_id, column_id, lowthreshold, lowthreshold_operator, threshold_type, highthreshold, highthreshold_operator) FROM stdin;
11	2	2	0	>=	low threat level	300	<=
13	2	2	600	<=	moderate threat level	301	>=
15	25	31	500000000	>	high threat level	\N	=
16	2	5	150000	>	high threat level	\N	=
17	2	5	150000	>=	low threat level	\N	=
\.


--
-- Data for Name: user_identification; Type: TABLE DATA; Schema: public; Owner: openlgu
--

COPY user_identification (username, password, role, area_id, userid) FROM stdin;
admin	admin	admin	\N	1
Spencer Hastings	hastings	dataencoder	2	2
mayor	mayor	LCE	\N	3
\.


--
-- Name: user_identification_userid_seq; Type: SEQUENCE SET; Schema: public; Owner: openlgu
--

SELECT pg_catalog.setval('user_identification_userid_seq', 3, true);


--
-- Name: alert_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY alert
    ADD CONSTRAINT alert_pkey PRIMARY KEY (alert_id);


--
-- Name: area_area_name_key; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY area
    ADD CONSTRAINT area_area_name_key UNIQUE (area_name);


--
-- Name: area_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY area
    ADD CONSTRAINT area_pkey PRIMARY KEY (area_id);


--
-- Name: column_dimension_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY column_dimension
    ADD CONSTRAINT column_dimension_pkey PRIMARY KEY (column_id);


--
-- Name: column_hierarchy_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY column_hierarchy
    ADD CONSTRAINT column_hierarchy_pkey PRIMARY KEY (column_hierarchy_id);


--
-- Name: indicator_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY threshold
    ADD CONSTRAINT indicator_pkey PRIMARY KEY (threshold_id);


--
-- Name: measure_measure_name_key; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY measure
    ADD CONSTRAINT measure_measure_name_key UNIQUE (measure_name);


--
-- Name: measure_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY measure
    ADD CONSTRAINT measure_pkey PRIMARY KEY (measure_id);


--
-- Name: row_dimension_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY row_dimension
    ADD CONSTRAINT row_dimension_pkey PRIMARY KEY (row_id);


--
-- Name: row_hierarchy_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY row_hierarchy
    ADD CONSTRAINT row_hierarchy_pkey PRIMARY KEY (row_hierarchy_id);


--
-- Name: user_identification_pkey; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY user_identification
    ADD CONSTRAINT user_identification_pkey PRIMARY KEY (userid);


--
-- Name: user_identification_username_key; Type: CONSTRAINT; Schema: public; Owner: openlgu; Tablespace: 
--

ALTER TABLE ONLY user_identification
    ADD CONSTRAINT user_identification_username_key UNIQUE (username);


--
-- Name: development_fund_trigger; Type: TRIGGER; Schema: public; Owner: openlgu
--

CREATE TRIGGER development_fund_trigger AFTER INSERT ON development_fund FOR EACH ROW EXECUTE PROCEDURE development_fund_trigger_function();


--
-- Name: enrollment_rate_in_secondary_schools_trigger; Type: TRIGGER; Schema: public; Owner: openlgu
--

CREATE TRIGGER enrollment_rate_in_secondary_schools_trigger AFTER INSERT ON enrollment_rate_in_secondary_schools FOR EACH ROW EXECUTE PROCEDURE enrollment_rate_in_secondary_schools_trigger_function();


--
-- Name: household_sewage_disposal_trigger; Type: TRIGGER; Schema: public; Owner: openlgu
--

CREATE TRIGGER household_sewage_disposal_trigger AFTER INSERT ON household_sewage_disposal FOR EACH ROW EXECUTE PROCEDURE household_sewage_disposal_trigger_function();


--
-- Name: rat_infestation_damage_trigger; Type: TRIGGER; Schema: public; Owner: openlgu
--

CREATE TRIGGER rat_infestation_damage_trigger AFTER INSERT ON rat_infestation_damage FOR EACH ROW EXECUTE PROCEDURE rat_infestation_damage_trigger_function();


--
-- Name: solid_waste_disposal_trigger; Type: TRIGGER; Schema: public; Owner: openlgu
--

CREATE TRIGGER solid_waste_disposal_trigger AFTER INSERT ON solid_waste_disposal FOR EACH ROW EXECUTE PROCEDURE solid_waste_disposal_trigger_function();


--
-- Name: statement_of_expenditures_trigger; Type: TRIGGER; Schema: public; Owner: openlgu
--

CREATE TRIGGER statement_of_expenditures_trigger AFTER INSERT ON statement_of_expenditures FOR EACH ROW EXECUTE PROCEDURE statement_of_expenditures_trigger_function();


--
-- Name: alert_measure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY alert
    ADD CONSTRAINT alert_measure_id_fkey FOREIGN KEY (measure_id) REFERENCES measure(measure_id);


--
-- Name: column_dimension_measure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY column_dimension
    ADD CONSTRAINT column_dimension_measure_id_fkey FOREIGN KEY (measure_id) REFERENCES measure(measure_id);


--
-- Name: indicator_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY threshold
    ADD CONSTRAINT indicator_column_id_fkey FOREIGN KEY (column_id) REFERENCES column_dimension(column_id);


--
-- Name: measure_area_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY measure
    ADD CONSTRAINT measure_area_id_fkey FOREIGN KEY (area_id) REFERENCES area(area_id);


--
-- Name: row_dimension_measure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY row_dimension
    ADD CONSTRAINT row_dimension_measure_id_fkey FOREIGN KEY (measure_id) REFERENCES measure(measure_id);


--
-- Name: threshold_measure_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY threshold
    ADD CONSTRAINT threshold_measure_id_fkey FOREIGN KEY (measure_id) REFERENCES measure(measure_id);


--
-- Name: user_identification_area_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: openlgu
--

ALTER TABLE ONLY user_identification
    ADD CONSTRAINT user_identification_area_id_fkey FOREIGN KEY (area_id) REFERENCES area(area_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

