# TwittPay for Tutor LMS

Tutor LMS payment gateway

Part of the [TwittPay](https://twittpay.com) addon family.

## Quick start

1. Download the latest zip from the **Releases** page of this repository.
2. Install it on your Tutor LMS following the guide below.
3. Open the TwittPay settings and enter your **Brand Key**. You can get it from [your dashboard](https://twittpay.com/user/brands).
4. Make a small test payment to confirm everything works.

Payments are always re-verified on your server before an order or invoice is marked paid.

## Detailed installation guide

```text
===========================================================================
 TwittPay - Tutor LMS gateway
===========================================================================

 INSTALL
   1. WordPress admin > Plugins > Add New > Upload Plugin, choose the zip.
   2. Activate "Tutor LMS - TwittPay Gateway". Tutor LMS must be active.
   3. Tutor LMS > Settings > Payments > TwittPay:
        Display Name       what students see at checkout
        Brand Key          from your TwittPay dashboard, under Brands
   4. Set the Tutor currency to BDT, save, then make one small test payment.

 NOTES
   * TwittPay takes BDT.
   * Every payment is verified again on your server and must cover the order
     amount before the student gets access.
```
