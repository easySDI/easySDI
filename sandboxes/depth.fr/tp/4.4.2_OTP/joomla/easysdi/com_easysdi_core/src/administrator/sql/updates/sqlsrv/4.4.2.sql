ALTER TABLE [#__sdi_diffusion] ADD [otp] [int] NOT NULL DEFAULT 0;
ALTER TABLE [#__sdi_order_diffusion] ADD [otp] [nvarchar](1000);
ALTER TABLE [#__sdi_order_diffusion] ADD [otpchance] [int] DEFAULT 0;
INSERT [#__sdi_sys_productstate] ([id], [ordering], [state], [value]) VALUES (8, 8, 1, N'blocked');