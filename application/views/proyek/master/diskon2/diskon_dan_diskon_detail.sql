USE [ciputraEms]਍䜀伀ഀഀ
/****** Object:  Table [dbo].[diskon]    Script Date: 28/01/2019 11:23:47 AM ******/਍匀䔀吀 䄀一匀䤀开一唀䰀䰀匀 伀一ഀഀ
GO਍匀䔀吀 儀唀伀吀䔀䐀开䤀䐀䔀一吀䤀䘀䤀䔀刀 伀一ഀഀ
GO਍匀䔀吀 䄀一匀䤀开倀䄀䐀䐀䤀一䜀 伀一ഀഀ
GO਍䌀刀䔀䄀吀䔀 吀䄀䈀䰀䔀 嬀搀戀漀崀⸀嬀搀椀猀欀漀渀崀⠀ഀഀ
	[id] [int] IDENTITY(1,1) NOT NULL,਍ऀ嬀最漀氀开搀椀猀欀漀渀开椀搀崀 嬀椀渀琀崀 一伀吀 一唀䰀䰀Ⰰഀഀ
	[product_category_id] [int] NOT NULL,਍ऀ嬀搀攀猀挀爀椀瀀琀椀漀渀崀 嬀瘀愀爀挀栀愀爀崀⠀㈀㔀㔀⤀ 一唀䰀䰀Ⰰഀഀ
	[active] [bit] NOT NULL,਍ऀ嬀搀攀氀攀琀攀崀 嬀戀椀琀崀 一唀䰀䰀Ⰰഀഀ
 CONSTRAINT [PK_diskon] PRIMARY KEY CLUSTERED ਍⠀ഀഀ
	[id] ASC਍⤀圀䤀吀䠀 ⠀倀䄀䐀开䤀一䐀䔀堀 㴀 伀䘀䘀Ⰰ 匀吀䄀吀䤀匀吀䤀䌀匀开一伀刀䔀䌀伀䴀倀唀吀䔀 㴀 伀䘀䘀Ⰰ 䤀䜀一伀刀䔀开䐀唀倀开䬀䔀夀 㴀 伀䘀䘀Ⰰ 䄀䰀䰀伀圀开刀伀圀开䰀伀䌀䬀匀 㴀 伀一Ⰰ 䄀䰀䰀伀圀开倀䄀䜀䔀开䰀伀䌀䬀匀 㴀 伀一⤀ 伀一 嬀倀刀䤀䴀䄀刀夀崀ഀഀ
) ON [PRIMARY]਍ഀഀ
GO਍匀䔀吀 䄀一匀䤀开倀䄀䐀䐀䤀一䜀 伀䘀䘀ഀഀ
GO਍⼀⨀⨀⨀⨀⨀⨀ 伀戀樀攀挀琀㨀  吀愀戀氀攀 嬀搀戀漀崀⸀嬀搀椀猀欀漀渀开搀攀琀愀椀氀崀    匀挀爀椀瀀琀 䐀愀琀攀㨀 ㈀㠀⼀　㄀⼀㈀　㄀㤀 ㄀㄀㨀㈀㌀㨀㐀㜀 䄀䴀 ⨀⨀⨀⨀⨀⨀⼀ഀഀ
SET ANSI_NULLS ON਍䜀伀ഀഀ
SET QUOTED_IDENTIFIER ON਍䜀伀ഀഀ
CREATE TABLE [dbo].[diskon_detail](਍ऀ嬀椀搀崀 嬀椀渀琀崀 䤀䐀䔀一吀䤀吀夀⠀㄀Ⰰ㄀⤀ 一伀吀 一唀䰀䰀Ⰰഀഀ
	[diskon_id] [int] NOT NULL,਍ऀ嬀猀攀爀瘀椀挀攀开椀搀崀 嬀椀渀琀崀 一唀䰀䰀Ⰰഀഀ
	[parameter_id] [int] NULL,਍ऀ嬀渀椀氀愀椀崀 嬀椀渀琀崀 一唀䰀䰀Ⰰഀഀ
	[active] [bit] NULL,਍ऀ嬀搀攀氀攀琀攀崀 嬀戀椀琀崀 一唀䰀䰀Ⰰഀഀ
	[flag_umum_event] [bit] NULL,਍ऀ嬀挀漀愀开洀愀瀀瀀椀渀最开椀搀开搀椀猀欀漀渀崀 嬀椀渀琀崀 一唀䰀䰀Ⰰഀഀ
 CONSTRAINT [PK_diskon_detail] PRIMARY KEY CLUSTERED ਍⠀ഀഀ
	[id] ASC਍⤀圀䤀吀䠀 ⠀倀䄀䐀开䤀一䐀䔀堀 㴀 伀䘀䘀Ⰰ 匀吀䄀吀䤀匀吀䤀䌀匀开一伀刀䔀䌀伀䴀倀唀吀䔀 㴀 伀䘀䘀Ⰰ 䤀䜀一伀刀䔀开䐀唀倀开䬀䔀夀 㴀 伀䘀䘀Ⰰ 䄀䰀䰀伀圀开刀伀圀开䰀伀䌀䬀匀 㴀 伀一Ⰰ 䄀䰀䰀伀圀开倀䄀䜀䔀开䰀伀䌀䬀匀 㴀 伀一⤀ 伀一 嬀倀刀䤀䴀䄀刀夀崀ഀഀ
) ON [PRIMARY]਍ഀഀ
GO਍�