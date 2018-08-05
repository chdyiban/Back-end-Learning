****
# 08.04
##
array_count_values()     
$a=array("A","Cat","Dog","A","Dog");        
var_dump(array_count_values($a));       
//array(3) { ["A"]=> int(2) ["Cat"]=> int(1) ["Dog"]=> int(2) }    
    
array_combine()     
$fname=array("Bill","Steve","Mark");       
$age=array("60","56","31");      
$c=array_combine($fname,$age);     
var_dump(array_count_values($c));       
//array(3) { [60]=> int(1) [56]=> int(1) [31]=> int(1) }  
    
array_chunk()  
$cars=array("Volvo","BMW","Toyota","Honda","Mercedes","Opel");   
var_dump(array_chunk($cars,3));    
//array(2) {    
	[0]=> array(3) {    
		[0]=> string(5) "Volvo"    
		[1]=> string(3) "BMW"    
		[2]=> string(6) "Toyota"    
		}    
	[1]=> array(3) {    
		[0]=> string(5) "Honda"    
		[1]=> string(8) "Mercedes"   
		[2]=> string(4) "Opel"    
		 }    
	}    
   
array_replace()  
$a1=array("red","green");  
$a2=array("blue","yellow");  
var_dump(array_replace($a1,$a2));  
//array(2) { [0]=> string(4) "blue" [1]=> string(6) "yellow" }   
    
array_splice()  
$a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");  
$a2=array("A"=>"purple","B"=>"orange");  
array_splice($a1,0,2,$a2);  
var_dump($a1);  
//array(4) { [0]=> string(6) "purple" [1]=> string(6) "orange" ["c"]=> string(4) "blue" ["d"]=> string(6) "yellow" }   
//注释：不保留被替换数组中的键名。  
