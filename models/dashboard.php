<?php

class Dashboard
{

	public function customer_detail_get ($customer_id )
	{
		$customer = (int) $customer_id;

		global $db;
		$sql = "
			select		CustomerID,
						CustomerNumber,
						CustomerName,
						LogoPath
			from		dbo.INF_Customers with (nolock)
			where		CustomerID = {$customer_id}
			option 		(maxdop 1)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function question_respondent_summary_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
	    $sql = "
	        select      replace(replace(QuestionText, char(13), ''), char(10), '') as QuestionText,
	                    QuestionType,
	                    count(1) as Respondents,
	                    round(avg(
				            case
								when QuestionType = 'Yes/No' and Answer = 'Not Answered'
									then null
								when QuestionType = 'Yes/No'
									then cast(replace(replace(nullif(nullif(AnswerRaw, 9999), 0), 1, 0), 2, 1) as decimal(10, 2))
								else cast(nullif(AnswerRaw, 9999) as decimal(10, 2))
							end
	                    ), 2) as AvgScore
	        from        dbo.v_SurveyDashboardDetail with (nolock, forceseek)
	        where       1=1
	                    and CustomerID = {$customer_id}
	                    and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
	                    and CompletedDate is not null
	                    and QuestionText is not null
	                    and AppCustomerEmail not like '%@ceojuice.com'
	                    and SurveyType <> 'Employee'
	        ";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    		and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    		and SurveyCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

	    $sql .= "
	        group by    QuestionText,
	                    QuestionType
	        order by    QuestionText
	        option		(maxdop 2)
	        ";

			$beg = microtime( true );
			$res = $db->query( $sql );
			$end = microtime( true );
			$dif = round( $end - $beg, 4 );
	
			$this->sql_dump( $sql, __FUNCTION__, $dif);
	
			return $res;
	}

	public function question_respondent_summary_by_month_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
	    $sql = "
	        select      left(datename(month, CreateDate), 3) + ' ' + right(cast(year(CreateDate) as varchar), 2) as CreateMonthText,
						cast(year(CreateDate) as varchar) + right('0' + cast(month(CreateDate) as varchar), 2) as CreateMonth,
	        			replace(replace(QuestionText, char(13), ''), char(10), '') as QuestionText,
	                    QuestionType,
	                    count(1) as Respondents,
	                    round(avg(
				            case
								when QuestionType = 'Yes/No' and Answer = 'Not Answered'
									then null
								when QuestionType = 'Yes/No'
									then cast(replace(replace(nullif(nullif(AnswerRaw, 9999), 0), 1, 0), 2, 1) as decimal(10, 2))
								else cast(nullif(AnswerRaw, 9999) as decimal(10, 2))
							end
	                    ), 2) as AvgScore
	        from        dbo.v_SurveyDashboardDetail with (nolock, forceseek)
	        where       1=1
	                    and CustomerID = {$customer_id}
	                    and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
	                    and CompletedDate is not null
	                    and QuestionText is not null
	                    and AppCustomerEmail not like '%@ceojuice.com'
	                    and SurveyType <> 'Employee'
	        ";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    		and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    		and SurveyCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

	    $sql .= "
	        group by    left(datename(month, CreateDate), 3) + ' ' + right(cast(year(CreateDate) as varchar), 2),
	        			cast(year(CreateDate) as varchar) + right('0' + cast(month(CreateDate) as varchar), 2),
	        			QuestionText,
	                    QuestionType
	        order by    QuestionText
	        option		(maxdop 2)
	        ";

			$beg = microtime( true );
			$res = $db->query( $sql );
			$end = microtime( true );
			$dif = round( $end - $beg, 4 );
	
			$this->sql_dump( $sql, __FUNCTION__, $dif);
	
			return $res;
	}

	public function question_respondent_detail_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
	    $sql = "
			select      Survey,
						replace(replace(QuestionText, char(13), ''), char(10), '') as QuestionText,
			            QuestionType,
			            Answer,
			            SurveyCustomerName,
			            TechnicianName,
			            AppCustomerEmail as CustomerEmail,
			            cast(CreateDate as date) as CreateDate,
			            cast(CompletedDate as date) as CompletedDate,
			            cast(Comment as varchar(1000)) as Comment
			from        dbo.v_SurveyDashboardDetail with (nolock, forceseek)
			where       1=1
			            and CustomerID = {$customer_id}
			            and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
			            and CompletedDate is not null
			            and QuestionText is not null
			            and AppCustomerEmail not like '%@ceojuice.com'
			            and SurveyType <> 'Employee'
	        ";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and SurveyCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

	    $sql .= "
					order by	Survey,
								AuthenticationID
					option		(maxdop 2)
	        ";

			$beg = microtime( true );
			$res = $db->query( $sql );
			$end = microtime( true );
			$dif = round( $end - $beg, 4 );
	
			$this->sql_dump( $sql, __FUNCTION__, $dif);
	
			return $res;
	}

	public function question_results_rollup_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
	    $sql = "
	        select      QuestionText,
	                    QuestionType,
	                    Answer,
	                    AnswerRaw,
	                    count(1) as Respondents,
	                    row_number() over (partition by QuestionText order by AnswerRaw) as Occurance,
	                    row_number() over (partition by QuestionText order by AnswerRaw desc) as OccuranceDesc
	        from        dbo.v_SurveyDashboardDetail with (nolock, forceseek)
	        where       1=1
	                    and CustomerID = {$customer_id}
	                    and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
	                    and CompletedDate is not null
	                    and QuestionText is not null
	                    and AppCustomerEmail not like '%@ceojuice.com'
	                    and SurveyType <> 'Employee'
	        ";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and SurveyCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

	    $sql .= "
	        group by    QuestionText,
	                    QuestionType,
	                    Answer,
	                    AnswerRaw
	        order by    QuestionType,
	        			QuestionText,
	                    AnswerRaw desc
	        option		(maxdop 2)
	        ";

			$beg = microtime( true );
			$res = $db->query( $sql );
			$end = microtime( true );
			$dif = round( $end - $beg, 4 );
	
			$this->sql_dump( $sql, __FUNCTION__, $dif);
	
			return $res;
	}

	public function survey_list_get ( $customer_id, /*optional*/ $survey_id_array = [] )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );

		global $db;

		/*
		$sql = "
			select		distinct
						SurveyID,
						Survey
			from		dbo.v_SurveyDashboardDetail with (nolock, forceseek)
			where		CustomerID = {$customer_id}
						and AppCustomerEmail not like '%@ceojuice.com'
		";
		*/

		$sql = "
			select		distinct
						sv.SurveyID as SurveyID,
						sv.Survey as Survey,
						sv.Description as Description
			from		dbo.SV_Surveys sv with (nolock)
						inner join dbo.INF_Customers cu with (nolock)
							on sv.CEOJuiceCustomerID = cu.CustomerID
			inner join	dbo.SV_AuthenticationTable at with (nolock)
							on sv.CEOJuiceCustomerID = at.CEOJuiceCustomerID
							and sv.SurveyID = at.SurveyID
			where		cu.CustomerID = {$customer_id}
						and at.AppCustomerEmail not like '%@ceojuice.com'
		";



		if ( count( $survey_id_array ) > 0 )
		{
			$sql .= "
				and sv.SurveyID in ({$survey_id_list})
			";
		}

		$sql .= "
			order by	sv.Survey
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function customers_get ()
	{
		global $db;
	    $sql = "
		select		CustomerID,
					CustomerNumber,
					CustomerName
		from		dbo.INF_Customers cu with (nolock)
		order by	CustomerName
		option		(maxdop 2)
	    ";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function min_max_survey_dates_get ( $customer_id )
	{
		global $db;
		/*
		$sql = "
			select		convert(varchar, min(CreateDate), 101) as MinDate,
						convert(varchar, max(CreateDate), 101) as MaxDate
			from		dbo.v_SurveyDashboardDetail with (nolock, forceseek)
			where		CustomerID = {$customer_id}
						and AppCustomerEmail not like '%@ceojuice.com'
			option		(maxdop 2)
		";
		*/

		$sql = "
			select		convert(varchar, min(at.CreateDate), 101) as MinDate,
						convert(varchar, max(at.CreateDate), 101) as MaxDate
			from		dbo.SV_Surveys sv with (nolock)
						inner join dbo.INF_Customers cu with (nolock)
							on sv.CEOJuiceCustomerID = cu.CustomerID
			inner join	dbo.SV_AuthenticationTable at with (nolock, forceseek)
							on sv.CEOJuiceCustomerID = at.CEOJuiceCustomerID
							and sv.SurveyID = at.SurveyID
			where		CustomerID = {$customer_id}
						and at.AppCustomerEmail not like '%@ceojuice.com'
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function survey_years_get ( $customer_id )
	{
		global $db;
		$sql = "
			select		year(CreateDate) as CreateYear
			from		dbo.v_SurveyDashboardDetail with (nolock, forceseek)
			where		CustomerID = {$customer_id}
						and AppCustomerEmail not like '%@ceojuice.com'
			group by	year(CreateDate)
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function dashboard_defaults_get ()
	{
		global $db;
		$sql = "
			declare	@MonthsBack date
			set		@MonthsBack = dateadd(month, -1, current_timestamp)
			select	cast(month(@MonthsBack) as varchar) + '/1/' + cast(year(@MonthsBack) as varchar) as DateFrom,
					convert(varchar, current_timestamp, 101) as DateTo
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_overall_monthly_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		left(datename(month, CreateDate), 3) + ' ' + right(cast(year(CreateDate) as varchar), 2) as CreateMonthText,
						CreateYear,
						CreateMonth,
						sum(NPSValue) as NPSValueTotal,
						count(1) as Rows,
						cast(sum(NPSValue) * 1.0 / count(1)  * 100 as decimal(10,1)) as NPSScore
			from		dbo.v_SurveyResponseUltimateQuestion with (nolock, forceseek)
			where		CEOJuiceCustomerID = {$customer_id}
						and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and CompletedDate is not null
						and NPSValue is not null
						and AppCustomerEmail not like '%@ceojuice.com'
						and SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			group by	left(datename(month, CreateDate), 3) + ' ' + right(cast(year(CreateDate) as varchar), 2),
						CreateYear,
						CreateMonth
			order by	CreateYear, CreateMonth
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_overall_yearly_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			declare	@TenYearsBack datetime
			set		@TenYearsBack = '1/1/' + cast(year(dateadd(year, -10, current_timestamp)) as varchar)
			
			select		year(CreateDate) as CreateYear,
						sum(NPSValue) as NPSValueTotal,
						count(1) as Rows,
						cast(sum(NPSValue) * 1.0 / count(1)  * 100 as decimal(10,1)) as NPSScore
			from		dbo.v_SurveyResponseUltimateQuestion with (nolock, forceseek)
			where		CEOJuiceCustomerID = {$customer_id}
						and CompletedDate is not null
						and NPSValue is not null
						and AppCustomerEmail not like '%@ceojuice.com'
						and SurveyType <> 'Employee'
						--------------------------------
						-- limit to latest 10 years
						--------------------------------
						and CreateDate >= @TenYearsBack
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			group by	year(CreateDate)
			order by	CreateYear
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_overall_score_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		sum(qs.NPSValue) as NPSValueTotal,
						count(1) as Rows,
						cast(sum(qs.NPSValue) * 1.0 / count(1)  * 100 as decimal(10,1)) as NPSScore
			from		dbo.v_SurveyResponseUltimateQuestion qs with (nolock, forceseek)
			inner join	dbo.v_SurveyResponse sr with (nolock, forceseek) on sr.AnswerID = qs.AnswerID
			where		qs.CEOJuiceCustomerID = {$customer_id}
						and qs.CreateDate >= cast('{$date_from}' as date) and qs.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and qs.CompletedDate is not null
						and qs.AppCustomerEmail not like '%@ceojuice.com'
						and qs.NPSValue is not null
						and qs.SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and qs.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and qs.AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_promoters_detractors_rollup_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		Type,
						Rows
			from		(
						select		case when NPSValue = 1 then 'Promoter' when NPSValue = 0 then 'Passive' when NPSValue = -1 then 'Detractor' else 'N/A' end as Type,
									count(1) as Rows
						from		dbo.v_SurveyResponseUltimateQuestion qs with (nolock)
						inner join	dbo.v_SurveyResponse sr with (nolock) on sr.AnswerID = qs.AnswerID
						where		qs.CEOJuiceCustomerID = {$customer_id}
									and qs.CreateDate >= cast('{$date_from}' as date) and qs.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
									and qs.CompletedDate is not null
									and qs.AppCustomerEmail not like '%@ceojuice.com'
									and qs.SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    						and qs.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    						and qs.AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
									group by	case when NPSValue = 1 then 'Promoter' when NPSValue = 0 then 'Passive' when NPSValue = -1 then 'Detractor' else 'N/A' end
									) x
						order by	case when Type = 'N/A' then null else Type end
						option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_promoters_detractors_detail_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		sr.SurveyTitle,
						sr.EAutoCustomer,
						sr.EAutoTech,
						sr.SurveySentTo,
						sr.authenticationid,
						case when qs.Answer = 9999 then null else qs.Answer end as 'Answer',
						case when qs.Answer between 0 and 6 then 'Detractor' when qs.Answer in (7,8) then 'Passive' when qs.Answer in (9,10) then 'Promoter' else 'N/A' end as Type,
						cast(qs.CreateDate as date) as CreateDate,
						cast(qs.CompletedDate as date) as CompletedDate
			from		dbo.v_SurveyResponseUltimateQuestion qs with (nolock)
			inner join	dbo.v_SurveyResponse sr with (nolock) on sr.AnswerID = qs.AnswerID
			where		qs.CEOJuiceCustomerID = {$customer_id}
						and qs.CreateDate >= cast('{$date_from}' as date) and qs.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and qs.CompletedDate is not null
						and qs.AppCustomerEmail not like '%@ceojuice.com'
						and qs.SurveyType <> 'Employee'
						--and qs.NPSValue is not null
			";

			if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and qs.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and qs.AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			order by	sr.SurveyTitle,
						sr.CreateDate
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_by_customer_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		sr.EautoCustomer,
						sum(qs.NPSValue) as NPSValueTotal,
						count(1) as Rows,
						cast(sum(qs.NPSValue) * 1.0 / count(1)  * 100 as decimal(10,1)) as NPSScore
			from		dbo.v_SurveyResponseUltimateQuestion qs with (nolock, forceseek)
			inner join	dbo.v_SurveyResponse sr with (nolock, forceseek) on sr.AnswerID = qs.AnswerID
			where		qs.CEOJuiceCustomerID = {$customer_id}
						and qs.CreateDate >= cast('{$date_from}' as date) and qs.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and qs.CompletedDate is not null
						and qs.NPSValue is not null
						and qs.AppCustomerEmail not like '%@ceojuice.com'
						and qs.SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and qs.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			group by	sr.EautoCustomer
			order by	sr.EautoCustomer
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function survey_stats_summary_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );

		//this proc uses pipe delimited value instead of comma delimited
		$eauto_customer_name_list = $this->array_to_pipe_list( $eauto_customer_name );

		global $db;
		$sql = "
			exec rpt_ZCJ_SurveyStatsSummary {$customer_id}, '{$date_from}', '{$date_to}', '{$survey_id_list}', '{$eauto_customer_name_list}'
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function survey_no_responses_detail_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		sv.Survey,
						us.AppCustomerName,
						us.AppCustomerEmail,
						us.AuthenticationID,
						us.CreateDate
			from		dbo.v_SurveyResponseUniqueSurveys us with (nolock, forceseek)
			left join	dbo.SV_Surveys sv on us.SurveyID = sv.SurveyID
			where		us.CEOJuiceCustomerID = {$customer_id}
						and us.CreateDate >= cast('{$date_from}' as date) and us.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and us.RespondentID is null
						and us.AppCustomerEmail not like '%@ceojuice.com'
						and us.SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and us.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and us.AppCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			order by	sv.Survey, us.AppCustomerName, us.AppCustomerEmail, us.CreateDate
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function survey_opt_outs_detail_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select      r.RespondentID,
			            s.SurveyID,
			            s.Survey,
			            at.AuthenticationID,
			            at.AppCustomerName,
			            at.AppCustomerEmail,
			            at.CreateDate
			from        SV_Respondents r with (nolock)
			inner join  SV_Surveys s with (nolock) on r.SurveyID = s.SurveyID
			inner join	SV_SurveyTypes st with (nolock) on st.SurveyTypeID = s.SurveyTypeID
			inner join  SV_Answers a with (nolock) on r.RespondentID = a.RespondentID
			inner join  SV_SurveyQuestions q with (nolock) on q.QuestionID = a.QuestionID
			left join	SV_SurveyquestionCategories qc with (nolock) on qc.QuestionCategoryid = q.QuestionCategoryID
			inner join  SV_AuthenticationTable at with (nolock) on r.AuthenticationID = at.AuthenticationID
			inner join  INF_Customers c with (nolock) on r.CEOJuiceCustomerID = c.CustomerID
			where       c.CustomerID = {$customer_id}
			            and at.CreateDate >= cast('{$date_from}' as date) and at.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
			            and a.Rank = 2 --yes answers
			            and
			            (
			            qc.QuestionCategory = 'Remove Me'
			            or q.QuestionText like '%Remove Me%'
			            )
			            and st.SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and s.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and at.AppCustomerName = ({$eauto_customer_name_list})
		    	";
		    }

		$sql .= "
			order by	s.Survey, at.AppCustomerName, at.AppCustomerEmail, at.CreateDate
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function survey_clicked_but_not_submitted_detail_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql = "
			select		sv.Survey,
						us.AppCustomerName,
						us.AppCustomerEmail,
						us.AuthenticationID,
						us.CreateDate
			from		dbo.v_SurveyResponseUniqueSurveys us with (nolock, forceseek)
			left join	dbo.SV_Surveys sv on us.SurveyID = sv.SurveyID
			where		us.CEOJuiceCustomerID = {$customer_id}
						and us.CreateDate >= cast('{$date_from}' as date) and us.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and us.RespondentID is not null
						and Completed = 0
						and us.AppCustomerEmail not like '%@ceojuice.com'
						and us.SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and us.SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and us.AppCustomerName in ({$eauto_customer_name})
		    	";
		    }

		$sql .= "
			order by	sv.Survey, us.AppCustomerName, us.AppCustomerEmail, us.CreateDate
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function surveyed_eauto_customers_get ( $customer_id, $date_from, $date_to )
	{
		global $db;

		/*
		$sql = "
			select		distinct isnull(EAutoCustomer, '(blank)') as EAutoCustomer
			from		dbo.v_surveyResponse with (nolock)
			where		CustomerID = {$customer_id}
			order by	1
			option		(maxdop 2)
		";
		*/

		$sql = "
			select		distinct
						isnull(sat.AppCustomerName, '(blank)') as EAutoCustomer
			from		sv_answers sa with (nolock)
			inner join	sv_respondents sr with (nolock) on sa.respondentid = sr.respondentid and sa.surveyid = sr.surveyid
			inner join	sv_authenticationtable sat with (nolock) on sr.authenticationid = sat.authenticationid
			inner join	inf_CustomerSubscriptions cs with (nolock) on sa.surveyid = cs.surveyid and sat.ceojuicecustomerid = cs.customerid
			where		1=1
						and cs.CustomerID = {$customer_id}
						and sat.CreateDate >= cast('{$date_from}' as date) and sat.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
			order by 	1
			option		(maxdop 2)
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}


	public function survey_response_get ( $authentication_id )
	{
		global $db;
		$sql = "
			exec rpt_ZCJSurveyResponse {$authentication_id}
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function survey_count_by_email_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
		$sql =
			"
			select		SurveyCustomerName,
						AppCustomerEmail,
						count(distinct AuthenticationID) as SurveyResponses
			from		dbo.v_SurveyDashboardDetail with (nolock)
			where		CustomerID = {$customer_id}
						and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
						and CompletedDate is not null
						and AppCustomerEmail not like '%@ceojuice.com'
						and SurveyType <> 'Employee'
			";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    			and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    			and SurveyCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

		$sql .=
			"
						and AppCustomerEmail not like '%@ceojuice.com'
			group by	SurveyCustomerName,
						AppCustomerEmail
			option		(maxdop 2)
			";

			$beg = microtime( true );
			$res = $db->query( $sql );
			$end = microtime( true );
			$dif = round( $end - $beg, 4 );
	
			$this->sql_dump( $sql, __FUNCTION__, $dif);
	
			return $res;
	}


	public function question_respondent_summary_by_tech_get ( $customer_id, $date_from, $date_to, $survey_id_array, $eauto_customer_name )
	{
		//array to list
		$survey_id_list = implode( ',', $survey_id_array );
		$eauto_customer_name_list = $this->array_to_quoted_list( $eauto_customer_name );

		global $db;
	    $sql = "
			select	t.TechnicianName,
					t.CreateMonth,
					case
						when len(t.CreateMonth) = 6
						then left(datename(month, cast(left(t.CreateMonth, 4) as varchar) + cast(right(CreateMonth, 2) as varchar) + '01'), 3) + ' ' + left(t.CreateMonth, 4)
						when t.CreateMonth = 'Total'
						then 'Total'
						else null
					end as CreateMonthText,
					t.Respondents,
					t.AvgScore
			from
			(
				select			case
									when (grouping(TechnicianName) = 1) then 'Total'
									else isnull(TechnicianName, 'Unknown')
								end as TechnicianName,

								case
									when (grouping(cast(year(CreateDate) as varchar) + right('0' + cast(month(CreateDate) as varchar), 2)) = 1) then 'Total'
									else cast(year(CreateDate) as varchar) + right('0' + cast(month(CreateDate) as varchar), 2)
								end as CreateMonth,

				        		--replace(replace(QuestionText, char(13), ''), char(10), '') as QuestionText,

								count(1) as Respondents,
								round(avg(
									case
										when QuestionType = 'Yes/No' and Answer = 'Not Answered'
											then null
										when QuestionType = 'Yes/No'
											then cast(replace(replace(nullif(nullif(AnswerRaw, 9999), 0), 1, 0), 2, 1) as decimal(10, 2))
										else cast(nullif(AnswerRaw, 9999) as decimal(10, 2))
									end
								), 2) as AvgScore
					from        dbo.v_SurveyDashboardDetail with (nolock, forceseek)
					where       1=1
								and CustomerID = {$customer_id}
								and CreateDate >= cast('{$date_from}' as date) and CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
								and CompletedDate is not null
								and QuestionText is not null
								and AppCustomerEmail not like '%@ceojuice.com'
								and QuestionCategory = 'Rate Tech Overall'
								and SurveyType <> 'Employee'

	        ";

		    if ( count($survey_id_array) > 0 )
		    {
		    	$sql .= "
		    		and SurveyID in ({$survey_id_list})
		    	";
		    }

		    if ( $eauto_customer_name_list != '' )
		    {
		    	$sql .= "
		    		and SurveyCustomerName in ({$eauto_customer_name_list})
		    	";
		    }

	    $sql .= "
					group by    Rollup
								(
								TechnicianName,
				        		cast(year(CreateDate) as varchar) + right('0' + cast(month(CreateDate) as varchar), 2)
				        		--QuestionText
								)

				) t

				option		(maxdop 2)
	        ";

			$beg = microtime( true );
			$res = $db->query( $sql );
			$end = microtime( true );
			$dif = round( $end - $beg, 4 );
	
			$this->sql_dump( $sql, __FUNCTION__, $dif);
	
			return $res;
	}

	public function questions_for_tech_score_get( $customer_id )
	{
		global $db;
		$sql = "
			select	distinct QuestionText
			from	v_SurveyDashboardDetail with (nolock)
			where	1=1
					and CustomerID = {$customer_id}
					and QuestionCategory = 'Rate Tech Overall'
					and SurveyType <> 'Employee'
		";

		$this->sql_dump( $sql, __FUNCTION__ );
		$results = $db->query( $sql );
		return $results;
	}

	public function nps_by_period_range_get( $customer_id, $period_from, $period_to, $group_by = null, $licenses_group = null, $state = null )
	{
		// i used a nested query to use year and month in the first data set for performance reasons

		global $db;

		//variables
		$year_from = substr($period_from, 0, 4);
		$year_from = (int)$year_from;

		$month_from = substr($period_from, 4, 2);
		$month_from = (int)$month_from;

		//variable cleanup
		if ( strlen(trim($group_by)) == 0) { $group_by = null; }
		if ( strlen(trim($licenses_group)) == 0) { $licenses_group = null; }
		if ( strlen(trim($state)) == 0) { $state = null; }


		if ( $group_by != null )
		{
			$group_by_text = "{$group_by},";
		}
		else
		{
			$group_by_text = "";
		}

		$sql = "
			set nocount on

			select *
			into	#NPSCounts
			from 	v_ZCJ_NPSCounts

			select      {$group_by_text}
						sum(detractCount) as detractCount,
			            sum(promoteCount) as promoteCount,
			            sum(TotalNPSResponseCount) as TotalNPSResponseCount,
			            sum(TotalSurveysSent) as TotalSurveysSent,
			           	sum(TotalResponseCount) as TotalResponseCount,
			            (sum(promoteCount) - sum(detractCount)) / nullif(sum(TotalNPSResponseCount), 0) as NPSScore,
			            count(distinct CustomerID) as CustomerCount,
			            max(EautoLicenses) as EautoLicenses
			from        (
							select *
							from #NPSCounts
							where prdPeriod between {$period_from} and {$period_to}
						) t
			where       1=1
		";

	    if ( $licenses_group != null )
	    {
	    	if ( $licenses_group == 'No Size Info' )
		    {
		    	$sql .= "
		    		and isnull(EautoLicensesGroup, '') = ''
		    	";
		    }
	    	else if ( $licenses_group == '' )
		    {
		    	//nothing
		    }
		    else
		    {
		    	$sql .= "
		    		and EautoLicensesGroup = '{$licenses_group}'
		    	";
		    }
	    }

	    if ( $customer_id != null )
	    {
	    	$sql .= "
	    		and CustomerID = {$customer_id}
	    	";
	    }

	    if ( $state != null )
	    {
	    	$sql .= "
	    		and State_ID = '{$state}'
	    	";
	    }

	    if ( $group_by_text != "")
	    {
	    	$sql .= "
	    		group by {$group_by}
	    	";
	    }

	    if ( $group_by_text == "EautoLicensesGroup")
	    {
	    	$sql .= "
	    		order by EautoLicenses
	    	";
	    }
	    else
	    {
	    	$sql .= "
	    		order by 1
	    	";
	    }

		if ( 1 == 1 )
		{
			$sql .= "
			drop table #NPSCounts
			";
		}

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_eauto_license_groups_get()
	{
		global $db;
		$sql = "
			select  distinct
			        isnull(cast(EautoLicensesGroup as varchar(50)), 'No Size Info') as EautoLicensesGroup,
			        case
			            when EautoLicensesGroup = '100+' then 2
			            when EautoLicensesGroup is null then 3
			            else 1
			        end as SortOrder
			from    v_ZCJ_NPSCounts
			order by
			        SortOrder, EautoLicensesGroup
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function nps_states_get()
	{
		global $db;
		$sql = "
			select  distinct
			        State_ID
			from    v_ZCJ_NPSCounts with (nolock)
			where   isnull(State_ID, '') <> ''
			order by 1
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}


	public function periods_get()
	{
		global $db;
		$sql = "
			set nocount on
            set transaction isolation level read uncommitted
			-----------------------------------
			-- get a list of periods
			-----------------------------------
			declare @CurrentLoop    int = 0

			declare	@MinPeriod int = (select min(prdPeriod) from v_ZCJ_NPSCounts where len(prdPeriod) = 6 )
            declare @MinPeriodDate date = cast(cast(@MinPeriod as varchar) + '01' as date)
            declare @MonthsToLoop int = datediff(month, @MinPeriodDate, current_timestamp)

			declare @PeriodList table
			(
			    DateValue datetime
			)

			while @CurrentLoop < @MonthsToLoop
			begin
			    insert into @PeriodList (DateValue)
			    values      (dateadd(month, -@CurrentLoop, current_timestamp))
			    set         @CurrentLoop = @CurrentLoop + 1
			end

			select
			        left(convert(varchar(8), DateValue, 112), 6) as Period,
			        left(datename(month, DateValue), 3) + ' - ' + cast(year(DateValue) as varchar(4)) as PeriodText
			from    @PeriodList
			order by Period
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}


	public function survey_comments_get( $customer_id, $date_from, $date_to, $sort_by, $sort_dir )
	{
		global $db;
		$sql = "
			select  sr.AnswerID,
					sr.QuestionID,
			        sr.AuthenticationID,
			        sr.SurveyTitle,
			        sr.QuestionText,
			        sr.ReferenceName,
			        sr.EAutoReference,
			        sr.EAutoCustomer,
			        sr.EAutoTech,
			        sr.SurveySentTo,
			        sr.QuestionText,
			        sr.Answer,
			        sr.TargetRank,
					sr.Comment,
			        sr.CreateDate,
			        sr.OkToShareComments,
			        sr.SortOrder,
			        an.Liked,
			        an.LikedIncludeQuestion,
			        an.LikedBy,
			        an.LikedDate
			from    v_SurveyResponse sr with (nolock)
			join	sv_Answers an on an.AnswerID = sr.AnswerID
			where   1=1
			        and sr.CustomerID = {$customer_id}
			        and sr.CreateDate >= cast('{$date_from}' as date) and sr.CreateDate < dateadd(day, 1, cast('{$date_to}' as date))
			        and len(sr.Comment) > 0
			        and sr.SurveyType <> 'Employee'
		";

		switch ($sort_by)
		{
			case 'survey-title':
				$sql = $sql . "order by	sr.SurveyTitle {$sort_dir}";
				break;

			case 'customer':
				$sql = $sql . "order by	sr.EautoCustomer {$sort_dir}";
				break;

			case 'sent-to':
				$sql = $sql . "order by	sr.SurveySentTo {$sort_dir}";
				break;

			case 'question':
				$sql = $sql . "order by	sr.QuestionText {$sort_dir}";
				break;

			case 'comment':
				$sql = $sql . "order by	sr.Comment {$sort_dir}";
				break;

			case 'answer':
				$sql = $sql . "order by	sr.Answer {$sort_dir}";
				break;

			case 'target':
				$sql = $sql . "order by	sr.TargetRank {$sort_dir}";
				break;

			case 'create-date':
				$sql = $sql . "order by	sr.CreateDate {$sort_dir}";
				break;

			case 'include-question':
				$sql = $sql . "order by	an.LikedIncludeQuestion {$sort_dir}";
				break;

			case 'like':
				$sql = $sql . "order by	case when an.Liked = 1 then 0 else 1 end {$sort_dir}";
				break;

			case 'dislike':
				$sql = $sql . "order by	case when an.Liked = -1 then 0 else 1 end {$sort_dir}";
				break;

			case 'liked-date':
				$sql = $sql . "order by	an.LikedDate {$sort_dir}";
				break;

			case 'liked-by':
				$sql = $sql . "order by	an.LikedBy {$sort_dir}";
				break;

			case 'ok-to-share-comments':
				$sql = $sql . "order by	sr.OkToShareComments {$sort_dir}";
				break;

			default:
				$sql = $sql . "order by sr.CreateDate desc, sr.SurveyTitle, sr.EAutoCustomer, sr.SurveySentTo, sr.SortOrder";
				break;

		}

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function comment_like_update( $answer_id, $question_id, $score, $email)
	{
		global $db;

		$sql = "

			declare	@Liked int
			set		@Liked = (select Liked from SV_Answers where AnswerID = {$answer_id} and QuestionID = {$question_id})

			if @Liked = {$score}
			begin
				update 	SV_Answers
				set		Liked = null,
						LikedIncludeQuestion = null,
						LikedDate = null,
						LikedBy = null
				where	AnswerID = {$answer_id}
						and QuestionID = {$question_id}
			end
			else
				update 	SV_Answers
				set		Liked = {$score},
						LikedDate = current_timestamp,
						LikedBy = '{$email}'
				where	AnswerID = {$answer_id}
						and QuestionID = {$question_id}
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function comment_include_question_update( $answer_id, $question_id )
	{
		global $db;

		$sql = "
			declare @Liked int
			declare	@LikedIncludeQuestion bit
			declare @AffectedRows int = 0

			select 	@Liked = Liked,
					@LikedIncludeQuestion = LikedIncludeQuestion
			from 	SV_Answers
			where AnswerID = {$answer_id} and QuestionID = {$question_id}

			------------------------------------------------
			--already included, so they are turning it off
			------------------------------------------------
			if @LikedIncludeQuestion = 1
			begin
				update 	SV_Answers
				set		LikedIncludeQuestion = null
				where	AnswerID = {$answer_id}
						and QuestionID = {$question_id}
			end
			------------------------------------------------
			--not included yet, so go ahead and include
			------------------------------------------------
			else if @Liked = 1 or @Liked = -1
			begin
				update 	SV_Answers
				set		LikedIncludeQuestion = 1
				where	AnswerID = {$answer_id}
						and QuestionID = {$question_id}

			end
		";

		$beg = microtime( true );
		$res = $db->query( $sql );
		$end = microtime( true );
		$dif = round( $end - $beg, 4 );

		$this->sql_dump( $sql, __FUNCTION__, $dif);

		return $res;
	}

	public function comment_include_question_get( $answer_id )
	{
		global $db;

		$sql = "
			select 	LikedIncludeQuestion
			from	SV_Answers
			where	AnswerID = {$answer_id}
		";

		$results = $db->query( $sql )->fetchAll();

        foreach ($results as $r)
        {
            $include_question = $r['LikedIncludeQuestion'];
        }

		return $include_question;
	}

	public function sql_dump( $sql, $function, $secs = null)
	{
		if ( $this->sql_dump_ok() )
		{
			echo '<pre>' .'<b>' . $function . ':</b> ' . $secs . '<br />' . $sql . '</pre>';
		}
	}

	public function sql_dump_ok()
	{
		$email = isset( $_SESSION['email'] ) == 1 ? $_SESSION['email'] : '';

	    if ( $_SERVER["COMPUTERNAME"] == 'JAVI-PC' )
	    {
	        $allow_everyone = 1;
	    }
	    else
	    {
	        $allow_everyone = 0;
	    }

	   	if ( (strpos( $email, 'ceojuice.com' ) > 0 || $allow_everyone == 1 ) && isset($_GET['debug']) )
	   	{
	    	$result = true;
		}
		else if (strpos( $email, 'javier@ceojuice.com' ) > 0)
		{
			$result = true;
	 	}
	    else
	    {
	    	$result = false;
	    }

	    return $result;
	}

	public function array_to_quoted_list( $array )
	{
		if ( $array[0] == '' || $array == '' )
		{
			$array_list = '';
		}
		else
		{
		    $array_new = [];
		    foreach( $array as $value )
		    {
		    	$value = str_replace("'", "''", $value);
		    	array_push( $array_new, $value );
		    	$array_list = "'". implode("','", $array_new) . "'";
		    }
		}

		return $array_list;
	}

	public function array_to_pipe_list( $array )
	{
		if ( $array[0] == '' )
		{
			$array_list = '';
		}
		else
		{
		    $array_new = [];
		    foreach( $array as $value )
		    {
		    	$value = str_replace("'", "''", $value);
		    	array_push( $array_new, $value );
		    }

			$array_list = implode("|", $array_new);
		}

		return $array_list;
	}
}


?>
