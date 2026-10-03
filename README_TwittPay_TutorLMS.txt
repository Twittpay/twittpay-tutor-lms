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
