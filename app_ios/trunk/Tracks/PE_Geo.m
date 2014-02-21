//
//  PE_Geo.m
//  VMote
//
//  Created by Savalas Colbert on 12/19/12.
//  Copyright (c) 2012 Pony Engine. All rights reserved.
//

#import "PE_Geo.h"

@implementation PE_Geo
@synthesize lat=_lat;
@synthesize lon=_lon;
@synthesize alt=_alt;

- (id)initWithJSON:(id)geoJSON
{
    NSLog(@"%@",geoJSON);
    self = [super init];
    if (self) {
        NSLog(@"The Lat is %@",[geoJSON valueForKey:@"lat"]);
        //VIDEO
        self.lat=[geoJSON valueForKey:@"lat"]!=[NSNull null]?[geoJSON valueForKey:@"lat"]:nil;
        
        self.lon=[geoJSON valueForKey:@"lon"]!=[NSNull null]?[geoJSON valueForKey:@"lon"]:nil;
       
        self.alt=[geoJSON valueForKey:@"alt"]!=[NSNull null]?[geoJSON valueForKey:@"alt"]:nil;
        
    }
    
    return self;
}

- (void) dealloc {
	[_lat release];
	[_lon release];
	[_alt release];
	[super dealloc];
}
@end
