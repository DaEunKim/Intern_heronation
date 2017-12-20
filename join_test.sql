select Product.Name, Category.NameKR, SizeType.NameKR 
from heronation.Product 
inner join heronation.Category on Product.CategoryID = Category.PKey
inner join heronation.SizeTypeList on Category.PKey = SizeTypeList.CategoryID
inner join heronation.SizeType on SizeTypeList.SizeTypeID = SizeType.PKey 
Where Product.PKey = 68;