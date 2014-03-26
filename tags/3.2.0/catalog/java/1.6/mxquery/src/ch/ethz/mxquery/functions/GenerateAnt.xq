<project name="MXQuery_Import">
	<target name="compile_functions">
	
	               <echo message="Copying Source Files..."/> 
	
	               <copy todir="${{customtmp.dir}}"><fileset dir="${{src.dir}}"/></copy>
	               <delete dir="${{tmpfunc.dir}}/fn"/>
	               <delete dir="${{tmpfunc.dir}}/mxq"/>
	               <delete dir="${{tmpfunc.dir}}/xs"/>	
	
		<echo message="Copying Function Gallery..."/> 
		{
			for $name in fn:doc("src/ch/ethz/mxquery/functions/FG_custom.xml")//functionDescription
			return
				<copy todir="${{tmpfunc.dir}}/{$name/functionPrefix}">
					<fileset file="${{srcfunc.dir}}/{$name/functionPrefix}/{$name/className}.java"/>
				</copy>
		}
		
		<copy file="${{srcfunc.dir}}/FG_custom.xml" tofile="${{tmpfunc.dir}}/FG_complete.xml" overwrite="yes">
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/DateTimeValues.java"/>
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/DayTimeDurationValues.java"/>
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/DistinctValuesIterator.java"/>
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/SubSequenceIterator.java"/>
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/CountIterator.java"/>
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/True.java"/>
  	               </copy>
  	               <copy todir="${{tmpfunc.dir}}/fn/">
  	                   <fileset file="${{srcfunc.dir}}/fn/False.java"/>
  	               </copy>  	               
	</target>
</project>