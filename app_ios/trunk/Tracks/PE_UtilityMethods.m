//
//  PE_UtilityMethods.m
//  PonyEngineFoundation
//
//  Created by Savalas Colbert on 12/12/13.
//  Copyright (c) 2013 PonyEngine. All rights reserved.
//

#import "PE_UtilityMethods.h"

@implementation PE_UtilityMethods

+(NSString *)DistanceOfTimeInWordsfromTime:(int)fromTime toTime:(int)toTime showLessThanAMinute:(bool)showLessThanAMinute {
   int distanceInSeconds = abs(toTime-fromTime);   //    [round(abs(toTime - fromTime));
   NSNumber *distanceInMinutes =[[NSNumber alloc] initWithFloat:round(distanceInSeconds / 60)];
   
   if ( [distanceInMinutes integerValue] <= 1 ) {
      if ( !showLessThanAMinute ) {
         return (distanceInMinutes == 0) ? @"less than a min" : @"1 min";
      } else {
         if ( distanceInSeconds < 5 ) {
            return @"less than 5 secs";
         }
         if ( distanceInSeconds < 10 ) {
            return @"less than 10 secs";
         }
         if ( distanceInSeconds < 20 ) {
            return @"less than 20 secs";
         }
         if ( distanceInSeconds < 40 ) {
            return @"about half a min";
         }
         if ( distanceInSeconds < 60 ) {
            return @"less than a min";
         }
         
         return @"1 min";
      }
   }
   if ( [distanceInMinutes integerValue] < 45 ) {
      return [NSString  stringWithFormat:@"%@ min",[distanceInMinutes stringValue]];
   }
   if ( [distanceInMinutes integerValue] < 90 ) {
      return @"1 hour";
   }
   if ( [distanceInMinutes integerValue] < 1440 ) {
      return [NSString stringWithFormat:@"%g hrs", round([distanceInMinutes floatValue] / 60.0)];
   }
   if ( [distanceInMinutes integerValue] < 2880 ) {
      return @"1 day";
   }
   if ( [distanceInMinutes integerValue] < 43200 ) {
      return [NSString stringWithFormat:@"%g days", round([distanceInMinutes floatValue]/ 1440)];
   }
   if ( [distanceInMinutes integerValue] < 86400 ) {
      return @"1 month";
   }
   if ( [distanceInMinutes integerValue] < 525600 ) {
      return [NSString stringWithFormat:@"%g mnths", round([distanceInMinutes floatValue]/ 43200)];
   }
   if ( [distanceInMinutes integerValue] < 1051199 ) {
      return @"1 year";
   }
   return [NSString stringWithFormat:@"%g yrs", round([distanceInMinutes floatValue]/ 525600)];
}

+ (NSString *) GetDateFromUnixFormat:(NSNumber*) unixFormat
{
   
   NSDate *date = [NSDate dateWithTimeIntervalSince1970:[unixFormat intValue]];
   NSDateFormatter *dateFormatter = [[NSDateFormatter alloc]init];
   [dateFormatter setDateFormat:@" E, MMM dd,yyyy @ h:mm a"];
   [dateFormatter setTimeZone:[NSTimeZone timeZoneForSecondsFromGMT:0]];
   //NSDate *date = [dateFormatter dateFromString:publicationDate];
   NSString *dte=[dateFormatter stringFromDate:date];
   
   return dte;
   
}


+(NSNumber *) GetDistancelat1:(double)lat1 lon1:(double)lon1 lat2:(double)lat2 lon2:(double)lon2 inMiles:(bool)inMiles{
   double pi80 = M_PI / 180;
   lat1 *= pi80;
   lon1 *= pi80;
   lat2 *= pi80;
   lon2 *= pi80;
   
   double r = 6372.797; // mean radius of Earth in km
   double dlat = lat2 - lat1;
   double dlon = lon2 - lon1;
   double a = sin(dlat / 2) * sin(dlat / 2) + cos(lat1) * cos(lat2) * sin(dlon / 2) * sin(dlon / 2);
   double c = 2 * atan2(sqrt(a), sqrt(1 - a));
   
   double km = r * c;
   
   double distance=(inMiles ? (km * 0.621371192) : km);
   //NSString *metric=inMiles?[[NSString alloc] initWithString:@"miles"]:[[NSString alloc] initWithString:@"km"];
   //return number_format($distance,2,'.','');
   
   return [NSNumber numberWithDouble:distance]; //[NSString stringWithFormat:@"%0.00f %@",distance,metric];
}


+(UIView *)CreateWaitScreenWithWords:(NSString *)waitWords andSuperView:(UIView *)theSuperView{
   int screenHeight=120;
   int screenWidth=120;
   int screenXPosition=(theSuperView.frame.size.width/2)-(screenWidth/2);
   int screenYPosition=(theSuperView.frame.size.height/4)-(screenHeight/4);
   
   UIView *aView=[[UIView alloc] initWithFrame:CGRectMake(screenXPosition,screenYPosition,screenWidth,screenHeight)];
   
   //Text Label
   int labelWidth=screenWidth-30;
   int labelHeight=50;
   int labelXPosition=15;
   int labelYPosition=50;
   UILabel *aLabel=[[UILabel alloc] initWithFrame:CGRectMake(labelXPosition, labelYPosition, labelWidth,labelHeight)];
   aLabel.backgroundColor=[UIColor clearColor];
   aLabel.textColor=[UIColor whiteColor];
   aLabel.text=waitWords;
   aLabel.adjustsFontSizeToFitWidth=YES;
   //aLabel.lineBreakMode=UILineBreakModeWordWrap;
   aLabel.numberOfLines=1;
   //aLabel.textAlignment=UITextAlignmentCenter;
   
   //Indicator
   int indicHeight=70;
   int indicWidth=70;
   int indicXPosition=(aView.frame.size.width/2)-(indicHeight/2);
   int indicYPosition=(aView.frame.size.height/4)-(indicWidth/4);
   UIActivityIndicatorView *anIndicator=[[UIActivityIndicatorView alloc] initWithFrame:CGRectMake(indicXPosition, indicYPosition, indicHeight, indicWidth)];
   //anIndicator.color=[UIColor whiteColor];
   aView.backgroundColor=[UIColor blackColor];
   aView.layer.cornerRadius=5;
   aView.alpha=.75;
   
   [aView addSubview:aLabel];
   
   [aView addSubview:anIndicator];
   [anIndicator startAnimating];

   [theSuperView addSubview:aView];
   
   return aView;
}

+(UIView *)CreateOverlayWithSubView:(UIView *)subView{
   UIWindow *frontWindow=[[[UIApplication sharedApplication] windows] lastObject];
   int screenHeight=frontWindow.frame.size.height;
   int screenWidth=frontWindow.frame.size.width;
   int screenXPosition=0;
   int screenYPosition=0;
   
   UIView *topHoverView = [[UIView alloc] initWithFrame:CGRectMake(screenXPosition,screenYPosition,screenWidth,screenHeight)];
   topHoverView.backgroundColor = [UIColor clearColor];
   [topHoverView setAlpha:1.0];
   [frontWindow addSubview:topHoverView];
   
   //Add Translucent View
   UIView *aView=[[UIView alloc] initWithFrame:CGRectMake(screenXPosition,screenYPosition,screenWidth,screenHeight)];
   
   aView.backgroundColor=[UIColor blackColor];
   aView.alpha=.55;
   [topHoverView addSubview:aView];
   
   
   [topHoverView addSubview:subView];
   
   return topHoverView;
}

/*+(NSString *)GetServiceHost:(BOOL)withHTTP{
 NSString *hostname=withHTTP?@"http://service.bumptrack.com/api/usersec":@"service.bumptrack.com/api/usersec";
 return hostname;
 }*/

+(NSString *)FormatLat:(double )theLat andLon:(double)theLon{
   int degrees = theLat;
   double decimal = fabs(theLat - degrees);
   int minutes = decimal * 60;
   double seconds = decimal * 3600 - minutes * 60;
   NSString *lat = [NSString stringWithFormat:@"%d° %d' %1.2f\"",
                    degrees, minutes, seconds];
   degrees = theLon;
   decimal = fabs(theLon - degrees);
   minutes = decimal * 60;
   seconds = decimal * 3600 - minutes * 60;
   NSString *longt = [NSString stringWithFormat:@"%d° %d' %1.2f\"",
                      degrees, minutes, seconds];
   
   
   return [NSString stringWithFormat:@"(%@,%@)",lat,longt];
}


+(UIImageView *)CreateLogoNavBarViewWithBar:(UINavigationBar *)thisNavBar{
   UIImage *navBarLogo=[UIImage imageNamed:@"1.1.0-logo-pressed.png"];
   
   CGRect logoFrame=CGRectMake(100,10, navBarLogo.size.width,navBarLogo.size.height);
   UIImageView *navBarLogoView=[[UIImageView alloc] initWithFrame:logoFrame];
   navBarLogoView.image=navBarLogo;
   [thisNavBar addSubview:navBarLogoView];
   return navBarLogoView; //Return handle so that it can be controlled
}

/*+(UIImageView *)CreateLogoNavBarViewWithBar:(UINavigationBar *)thisNavBar{
 UIImage *navBarLogo=[UIImage imageNamed:@"1.1.0-logo-pressed.png"];
 CGRect logoFrame=CGRectMake(10, 10, navBarLogo.size.width,navBarLogo.size.height);
 UIImageView *navBarLogoView=[[UIImageView alloc] initWithFrame:logoFrame];
 navBarLogoView.image=navBarLogo;
 [thisNavBar addSubview:navBarLogoView];
 return navBarLogoView; //Return handle so that it can be controlled
 }*/

+(bool)NSNumber_in_ArrayValueNSNumber:(NSNumber *)newNumber in_array:(NSMutableArray *)an_Array{
   for (NSNumber *aNumber in an_Array){
      int number1=[aNumber integerValue];
      int number2=[newNumber integerValue];
      if(number1==number2){
         return YES;
      }
   }
   return NO;
}



+(UILabel *)CreateNameNavBarLabelWithBar:(UINavigationBar *)thisNavBar{
   CGRect nameFrame;
   if ([[UIDevice currentDevice].model isEqualToString:@"iPhone"]) {
      nameFrame=CGRectMake(57, 5, 135,30);
   }else{
      //iPad
      if([[UIDevice currentDevice] orientation]==UIDeviceOrientationPortrait){
         nameFrame=CGRectMake(57, 5, 135,30);
      }else{ //landscape
         nameFrame=CGRectMake(200, 5, 135,30);
      }
   }
   
   UILabel *navBarNameLabel=[[UILabel alloc] initWithFrame:nameFrame];
   navBarNameLabel.backgroundColor=[UIColor clearColor];
   navBarNameLabel.textColor=[UIColor whiteColor];
   navBarNameLabel.adjustsFontSizeToFitWidth=YES;
   [thisNavBar addSubview:navBarNameLabel];
   return navBarNameLabel; //Return handle so that it can be controlled
}


+(NSString *) getHumanReadableTimeFromTimeInterval: (NSTimeInterval) theTimeInterval {
   return [PE_UtilityMethods getHumanReadableTimeFromTimeInterval:theTimeInterval andType:1];
}

+(NSString *) getHumanReadableTimeFromTimeInterval: (NSTimeInterval) theTimeInterval andType:(int)viewType {
   //1= 00:00:00
   //2= 00h 00m 00s
   
   NSCalendar *myCalendar = [NSCalendar currentCalendar];
   NSDate *date1 = [[NSDate alloc] init];
   NSDate *date2 = [[NSDate alloc] initWithTimeInterval:theTimeInterval sinceDate:date1];
   
   // Get conversion to months, days, hours, minutes
   unsigned int unitFlags =NSHourCalendarUnit| NSMinuteCalendarUnit | NSSecondCalendarUnit;
   
   NSDateComponents *conversionInfo = [myCalendar components:unitFlags fromDate:date1  toDate:date2  options:0];
   NSString *minuteZeroSpace;
   NSString *secondZeroSpace=[conversionInfo second]>9?@"":@"0";
   
   
   NSString *timeString;
   NSString * hourString;
   switch (viewType) {
      case 2:
         timeString=[NSString stringWithFormat:@"%dh %dm %ds",[conversionInfo hour],[conversionInfo minute],[conversionInfo second]];
         break;
      case 1:
      default:
         if([conversionInfo hour]>0){
            hourString=[NSString stringWithFormat:@"%d:",[conversionInfo hour]];
            minuteZeroSpace=[conversionInfo minute]>9?@"":@"0";
         }else{
            hourString=@"";
            minuteZeroSpace=@"";
         }
         timeString=[NSString stringWithFormat:@"%@%@%d:%@%d",hourString,minuteZeroSpace,[conversionInfo minute],secondZeroSpace,[conversionInfo second]];
         break;
   }
   
   return timeString;
}


/*   +(BOOL)CheckBoolofJSON:(NSString *)JSONString{
 SBJsonParser *parser=[[SBJsonParser alloc] init];
 NSDictionary  *theResponse=[parser objectWithString:JSONString];
 
 if ([theResponse count]>0) { //if more than one spontt
 int boolData=[[theResponse objectForKey:@"bool"] intValue]; //Move to bool
 NSDictionary *theStatus=[theResponse objectForKey:@"status"];
 NSString *statusCode=[theStatus objectForKey:@"code"];
 if ([statusCode isEqualToString:@"200"]){
 return boolData==1?YES:NO;
 }else {
 NSLog(@"No user data was found");
 }
 }else {
 NSLog(@"Empty response");
 }
 return NO;
 }
 
 +(NSNumber *)ParseIntJSON:(NSString *)JSONString{
 SBJsonParser *parser=[[SBJsonParser alloc] init];
 NSDictionary  *theResponse=[parser objectWithString:JSONString];
 
 if ([theResponse count]>0) { //if more than one spontt
 NSString *intData=[theResponse objectForKey:@"int"];
 NSDictionary *theStatus=[theResponse objectForKey:@"status"];
 NSString *statusCode=[theStatus objectForKey:@"code"];
 if ([statusCode isEqualToString:@"200"]){
 return [NSNumber numberWithInt:[intData intValue]];
 }else {
 NSLog(@"No user data was found");
 }
 }else {
 NSLog(@"Empty response");
 }
 return 0;
 }
 */

+ (UIImage *)imageWithImage:(UIImage *)image scaledToSize:(CGSize)newSize {
   //UIGraphicsBeginImageContext(newSize);
   UIGraphicsBeginImageContextWithOptions(newSize, NO, 0.0);
   [image drawInRect:CGRectMake(0, 0, newSize.width, newSize.height)];
   UIImage *newImage = UIGraphicsGetImageFromCurrentImageContext();
   UIGraphicsEndImageContext();
   return newImage;
}


//Facebook
+(void)PostToMyWallwithTitle:(NSString *)titleName andCaption:(NSString *)caption andLinkURL:(NSString *)linkURL andImageURL:(NSString *)imgURL{
   //NSString *linkURL = [NSString stringWithFormat:@"https://www.friendsmash.com/challenge_brag_%llu", m_uPlayerFBID];
   //NSString *pictureURL = @"http://www.friendsmash.com/images/logo_large.jpg";
   
   // Prepare the native share dialog parameters
   FBShareDialogParams *shareParams = [[FBShareDialogParams alloc] init];
   shareParams.link = [NSURL URLWithString:linkURL];
   shareParams.name = @"I'm betting with my friend on BetOnIt";
   shareParams.caption= @"Come and bet with us at BetOnit.com";
   shareParams.picture= [NSURL URLWithString:imgURL];
   shareParams.description =@"I just bet on an NHL game for 2 Drinks";
   
   
   if ([FBDialogs canPresentShareDialogWithParams:shareParams]){
      
      [FBDialogs presentShareDialogWithParams:shareParams
                                  clientState:nil
                                      handler:^(FBAppCall *call, NSDictionary *results, NSError *error) {
                                         if(error) {
                                            NSLog(@"Error publishing story.");
                                         } else if (results[@"completionGesture"] && [results[@"completionGesture"] isEqualToString:@"cancel"]) {
                                            NSLog(@"User canceled story publishing.");
                                         } else {
                                            NSLog(@"Story published.");
                                         }
                                      }];
      
   }else {
      
      // Prepare the web dialog parameters
      NSDictionary *params = @{
                               @"name" : shareParams.name,
                               @"caption" : shareParams.caption,
                               @"description" : shareParams.description,
                               @"picture" : imgURL,
                               @"link" : linkURL
                               };
      
      // Invoke the dialog
      [FBWebDialogs presentFeedDialogModallyWithSession:nil
                                             parameters:params
                                                handler:
       ^(FBWebDialogResult result, NSURL *resultURL, NSError *error) {
          if (error) {
             NSLog(@"Error publishing story.");
          } else {
             if (result == FBWebDialogResultDialogNotCompleted) {
                NSLog(@"User canceled story publishing.");
             } else {
                NSLog(@"Story published.");
             }
          }}];
   }
}

+ (void)PostToWallOfFriendIds:(NSArray *)friends withTitle:(NSString *)titleName withMessage:(NSString *)message andLink:(NSString *)linkToPage
{
   
   for (id<FBGraphUser>aFriend in friends) {
      //Make the post.
      NSMutableDictionary* params = [[NSMutableDictionary alloc] initWithObjectsAndKeys:message, @"message", linkToPage, @"link",titleName, @"name", nil];
      
      NSLog(@"\nparams=%@\n", params);
      
      //Post to friend's wall.
      [FBRequestConnection startWithGraphPath:[NSString stringWithFormat:@"%@/feed", aFriend.id] parameters:params HTTPMethod:@"POST"
                            completionHandler:^(FBRequestConnection *connection, id result, NSError *error)
       {
          //Tell the user that it worked.
          UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:@"Shared"
                                                              message:[NSString stringWithFormat:@"Invited %@! error=%@", aFriend.name, error]
                                                             delegate:nil
                                                    cancelButtonTitle:@"OK"
                                                    otherButtonTitles:nil];
          
          [alertView show];
       }
       ];
      
      //Close the friend picker.
   }
   
}

//Math
+(int)SumArray:(NSArray *)anArray{
   int total=0;
   for (NSNumber *part in anArray) {
      total=[part floatValue]+total;
   }
   return total;
}


//Sharing
+(void)PostToFacebookWithObject:(id)graphObject andGraphPathnsobj:(NSString *)nameSpaceObject andMessage:(NSString *)msg{
   //Share on Facebook
   /* NSString *graphPath=[@"me/" stringByAppendingString:nameSpaceObject];
    
    // First create the Open Graph meal object for the meal we ate.
    id<PVOGVokel> vokelObject = (id<PVOGVokel>)[FBGraphObject graphObject];
    vokelObject.url=self.theVokelShare.vokelPage;
    
    // Now create an Open Graph eat action with the meal, our location,
    // and the people we were with.
    id<PVOGShareAction> action =
    (id<PVOGShareAction>)[FBGraphObject graphObject];
    action.vokel = vokelObject;
    
    [FBRequestConnection startForPostWithGraphPath:graphPath graphObject:action completionHandler: ^(FBRequestConnection *connection, id result, NSError *error) {
    if (!error) {
    // alertText = [NSString stringWithFormat:
    //@"Posted Open Graph action, id: %@",
    //[result objectForKey:@"id"]];
    NSDictionary *userInfo = [NSDictionary dictionaryWithObject:@"Vokel posted to Facebook"forKey:@"msg"];
    
    [[NSNotificationCenter defaultCenter]
    postNotificationName:@"appnotif"
    object:self
    userInfo:userInfo];
    
    } else {
    NSString *alertText;
    alertText = [NSString stringWithFormat:
    @"error: domain = %@, code = %d",
    error.domain, error.code];
    
    [[[UIAlertView alloc] initWithTitle:@"Result"
    message:alertText
    delegate:nil
    cancelButtonTitle:@"Thanks!"
    otherButtonTitles:nil]
    show];
    }
    }
    ];
    
    [self cancel:self];*/
}


+(void)PostStatusToFacebookWithMessage:(id)graphObject andGraphPathnsobj:(NSString *)nameSpaceObject andMessage:(NSString *)msg{
   
   
   /* NSMutableDictionary *postParams =  [[NSMutableDictionary alloc] initWithObjectsAndKeys:                     newsURL, @"link",
    newsTitle,@"name",nil];
    
    
    
    [postParams release];*/
   
   //Share on Facebook
   /* NSString *graphPath=[@"me/" stringByAppendingString:nameSpaceObject];
    
    // First create the Open Graph meal object for the meal we ate.
    id<PVOGVokel> vokelObject = (id<PVOGVokel>)[FBGraphObject graphObject];
    vokelObject.url=self.theVokelShare.vokelPage;
    
    // Now create an Open Graph eat action with the meal, our location,
    // and the people we were with.
    id<PVOGShareAction> action =
    (id<PVOGShareAction>)[FBGraphObject graphObject];
    action.vokel = vokelObject;
    
    [FBRequestConnection startForPostWithGraphPath:graphPath graphObject:action completionHandler: ^(FBRequestConnection *connection, id result, NSError *error) {
    if (!error) {
    // alertText = [NSString stringWithFormat:
    //@"Posted Open Graph action, id: %@",
    //[result objectForKey:@"id"]];
    NSDictionary *userInfo = [NSDictionary dictionaryWithObject:@"Vokel posted to Facebook"forKey:@"msg"];
    
    [[NSNotificationCenter defaultCenter]
    postNotificationName:@"appnotif"
    object:self
    userInfo:userInfo];
    
    } else {
    NSString *alertText;
    alertText = [NSString stringWithFormat:
    @"error: domain = %@, code = %d",
    error.domain, error.code];
    
    [[[UIAlertView alloc] initWithTitle:@"Result"
    message:alertText
    delegate:nil
    cancelButtonTitle:@"Thanks!"
    otherButtonTitles:nil]
    show];
    }
    }
    ];
    
    [self cancel:self];*/
}


+ (void)publishStory:(NSMutableDictionary *)postParams
{
   
   [FBRequestConnection
    startWithGraphPath:@"me/feed"
    parameters:postParams
    HTTPMethod:@"POST"
    completionHandler:^(FBRequestConnection *connection,
                        id result,
                        NSError *error) {
       NSString *alertText;
       if (error) {
          alertText = [NSString stringWithFormat:
                       @"error: domain = %@, code = %d",
                       error.domain, error.code];
          [TestFlight passCheckpoint:@"PublishStory- NoPost"];
       } else {
          alertText = [NSString stringWithFormat:
                       @"Posted action, id: %@",
                       result[@"id"]];
          [TestFlight passCheckpoint:@"PublishStory- Posted"];
       }
       // Show the result in an alert
       /* [[[UIAlertView alloc] initWithTitle:@"Result"
        message:alertText
        delegate:self
        cancelButtonTitle:@"OK!"
        otherButtonTitles:nil]
        show];*/
       //Report via TestFlight
    }];
}




@end
