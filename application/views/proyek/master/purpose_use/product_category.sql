��U S E   [ c i p u t r a E m s ]  
 G O  
 / * * * * * *   O b j e c t :     T a b l e   [ d b o ] . [ p r o d u c t _ c a t e g o r y ]         S c r i p t   D a t e :   2 8 / 0 1 / 2 0 1 9   1 1 : 3 3 : 4 0   A M   * * * * * * /  
 S E T   A N S I _ N U L L S   O N  
 G O  
 S E T   Q U O T E D _ I D E N T I F I E R   O N  
 G O  
 S E T   A N S I _ P A D D I N G   O N  
 G O  
 C R E A T E   T A B L E   [ d b o ] . [ p r o d u c t _ c a t e g o r y ] (  
 	 [ i d ]   [ i n t ]   I D E N T I T Y ( 1 , 1 )   N O T   N U L L ,  
 	 [ p r o j e c t _ i d ]   [ i n t ]   N O T   N U L L ,  
 	 [ n a m e ]   [ v a r c h a r ] ( 1 0 0 )   N O T   N U L L ,  
 	 [ d e s c r i p t i o n ]   [ v a r c h a r ] ( 2 5 5 )   N U L L ,  
 	 [ a c t i v e ]   [ b i t ]   N O T   N U L L ,  
 	 [ d e l e t e ]   [ b i t ]   N U L L ,  
   C O N S T R A I N T   [ P K _ p r o d u c t _ c a t e g o r i e s ]   P R I M A R Y   K E Y   C L U S T E R E D    
 (  
 	 [ i d ]   A S C  
 ) W I T H   ( P A D _ I N D E X   =   O F F ,   S T A T I S T I C S _ N O R E C O M P U T E   =   O F F ,   I G N O R E _ D U P _ K E Y   =   O F F ,   A L L O W _ R O W _ L O C K S   =   O N ,   A L L O W _ P A G E _ L O C K S   =   O N )   O N   [ P R I M A R Y ]  
 )   O N   [ P R I M A R Y ]  
  
 G O  
 S E T   A N S I _ P A D D I N G   O F F  
 G O  
 